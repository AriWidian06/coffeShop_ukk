<?php

namespace App\Http\Controllers;

use App\Models\KasAccount;
use App\Models\KasClosing;
use App\Models\KasMovement;
use App\Models\Pengeluaran;
use App\Models\Transaksi;
use App\Services\KasService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    public function index(KasService $kas)
    {
        $accounts = KasAccount::where('aktif', true)->orderBy('id')->get()
            ->map(function ($account) use ($kas) {
                $account->saldo_saat_ini = $kas->saldo($account);
                $account->ringkasan_hari_ini = $kas->ringkasanHarian($account, now('Asia/Jakarta'));
                return $account;
            });

        $totalSaldo = (float) $accounts->sum('saldo_saat_ini');

        $recentMovements = KasMovement::with(['account', 'karyawan'])
            ->latest('id')
            ->limit(10)
            ->get();

        $recentClosings = KasClosing::with(['account', 'karyawan', 'verifier'])
            ->latest('tanggal')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('kas.index', compact('accounts', 'totalSaldo', 'recentMovements', 'recentClosings'));
    }

    public function mutasi(Request $request)
    {
        $query = KasMovement::with(['account', 'karyawan'])
            ->when($request->filled('kas_account_id'), fn ($q) => $q->where('kas_account_id', $request->kas_account_id))
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('tanggal', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('tanggal', '<=', $request->end_date))
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        $totals = [
            'debit' => (float) (clone $query)->sum('debit'),
            'kredit' => (float) (clone $query)->sum('kredit'),
        ];
        $movements = $query->paginate(20)->withQueryString();
        $accounts = KasAccount::orderBy('id')->get();

        return view('kas.mutasi', compact('movements', 'accounts', 'totals'));
    }

    public function saldoAwal()
    {
        $this->authorize('kelola-kas');

        return view('kas.saldo-awal', [
            'accounts' => KasAccount::where('aktif', true)->orderBy('id')->get(),
        ]);
    }

    public function simpanSaldoAwal(Request $request, KasService $kas)
    {
        $this->authorize('kelola-kas');

        $validated = $request->validate([
            'kas_account_id' => 'required|exists:kas_accounts,id',
            'jumlah' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
        ]);

        $account = KasAccount::findOrFail($validated['kas_account_id']);
        $existing = $kas->saldo($account) > 0;
        $kas->catatSaldoAwal($account, (float) $validated['jumlah'], auth('karyawan')->id());

        return back()->with('success', $existing
            ? 'Saldo awal sudah tercatat; mutasi tidak digandakan.'
            : 'Saldo awal berhasil dicatat.');
    }

    public function transfer()
    {
        $this->authorize('kelola-kas');

        return view('kas.transfer', [
            'accounts' => KasAccount::where('aktif', true)->orderBy('id')->get(),
        ]);
    }

    public function simpanTransfer(Request $request, KasService $kas)
    {
        $this->authorize('kelola-kas');

        $validated = $request->validate([
            'dari' => 'required|exists:kas_accounts,id|different:ke',
            'ke' => 'required|exists:kas_accounts,id',
            'jumlah' => 'required|numeric|min:0.01',
            'keterangan' => 'required|string|max:255',
        ]);

        $kas->transfer(
            KasAccount::findOrFail($validated['dari']),
            KasAccount::findOrFail($validated['ke']),
            (float) $validated['jumlah'],
            $validated['keterangan'],
            auth('karyawan')->id()
        );

        return back()->with('success', 'Transfer antar kas berhasil dicatat.');
    }

    public function penyesuaian()
    {
        $this->authorize('kelola-kas');

        return view('kas.penyesuaian', [
            'accounts' => KasAccount::where('aktif', true)->orderBy('id')->get(),
        ]);
    }

    public function simpanPenyesuaian(Request $request, KasService $kas)
    {
        $this->authorize('kelola-kas');

        $validated = $request->validate([
            'kas_account_id' => 'required|exists:kas_accounts,id',
            'arah' => 'required|in:debit,kredit',
            'jumlah' => 'required|numeric|min:0.01',
            'keterangan' => 'required|string|max:255',
        ]);

        $kas->penyesuaian(
            KasAccount::findOrFail($validated['kas_account_id']),
            (float) $validated['jumlah'],
            $validated['arah'],
            $validated['keterangan'],
            auth('karyawan')->id()
        );

        return back()->with('success', 'Penyesuaian saldo berhasil dicatat.');
    }

    public function tutupHari(Request $request, KasService $kas)
    {
        $this->authorize('tutup-kas');

        $tanggal = $request->filled('tanggal') && preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->tanggal)
            ? Carbon::parse($request->tanggal)
            : now('Asia/Jakarta');

        $accounts = KasAccount::where('aktif', true)->orderBy('id')->get()
            ->map(function ($account) use ($kas, $tanggal) {
                $account->ringkasan = $kas->ringkasanHarian($account, $tanggal);
                $account->closing = KasClosing::where('kas_account_id', $account->id)
                    ->whereDate('tanggal', $tanggal->toDateString())
                    ->first();
                return $account;
            });

        return view('kas.tutup-hari', compact('accounts', 'tanggal'));
    }

    public function simpanTutupHari(Request $request, KasService $kas)
    {
        $this->authorize('tutup-kas');

        $validated = $request->validate([
            'kas_account_id' => 'required|exists:kas_accounts,id',
            'tanggal' => 'required|date',
            'saldo_fisik' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:500',
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);
        $ringkasan = $kas->ringkasanHarian(KasAccount::findOrFail($validated['kas_account_id']), $tanggal);
        $selisih = round((float) $validated['saldo_fisik'] - $ringkasan['saldo_buku'], 2);

        if (abs($selisih) > 0 && blank($validated['catatan'] ?? null)) {
            return back()->withErrors(['catatan' => 'Selisih kas wajib diberi catatan.']);
        }

        KasClosing::updateOrCreate(
            [
                'kas_account_id' => $validated['kas_account_id'],
                'tanggal' => $tanggal->toDateString(),
            ],
            [
                'saldo_awal' => $ringkasan['saldo_awal'],
                'total_masuk' => $ringkasan['total_masuk'],
                'total_keluar' => $ringkasan['total_keluar'],
                'saldo_buku' => $ringkasan['saldo_buku'],
                'saldo_fisik' => $validated['saldo_fisik'],
                'selisih' => $selisih,
                'status' => 'closed',
                'karyawan_id' => auth('karyawan')->id(),
                'verified_by' => null,
                'verified_at' => null,
                'catatan' => $validated['catatan'] ?? null,
            ]
        );

        return back()->with('success', 'Tutup hari berhasil disimpan. Menunggu verifikasi manager.');
    }

    public function verifikasi(Request $request, KasClosing $closing)
    {
        $this->authorize('verifikasi-kas');

        if ($closing->verified_at) {
            return back()->with('error', 'Closing ini sudah diverifikasi.');
        }

        $validated = $request->validate([
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $closing->update([
            'verified_by' => auth('karyawan')->id(),
            'verified_at' => now(),
            'catatan' => ($validated['catatan_verifikasi'] ?? null) ?: $closing->catatan,
        ]);

        return back()->with('success', 'Closing kas terverifikasi.');
    }

    public function laporan(Request $request, KasService $kas)
    {
        $this->authorize('lihat-laporan-kas');

        $tz = 'Asia/Jakarta';
        $start = Carbon::parse($request->input('start_date', now($tz)->startOfMonth()->toDateString()), $tz)->startOfDay();
        $end = Carbon::parse($request->input('end_date', now($tz)->endOfMonth()->toDateString()), $tz)->endOfDay();
        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $transaksis = Transaksi::with('detail_transaksis.produk')
            ->whereBetween('waktu_transaksi', [$start->copy()->utc(), $end->copy()->utc()])
            ->where('status_pesanan', '!=', 'cancelled')
            ->get();

        $pendapatan = (float) $transaksis->sum('total_harga');
        $hpp = (float) $transaksis->flatMap->detail_transaksis->sum(
            fn ($detail) => (int) $detail->QTY * (float) ($detail->produk->harga_beli ?? 0)
        );
        $labaKotor = round($pendapatan - $hpp, 2);

        $pengeluarans = Pengeluaran::approved()
            ->with(['account', 'karyawan'])
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $pengeluaranPerKategori = $pengeluarans->groupBy('kategori')
            ->map(fn ($group) => (float) $group->sum('jumlah'));
        $totalPengeluaran = (float) $pengeluarans->sum('jumlah');
        $labaBersih = round($labaKotor - $totalPengeluaran, 2);

        $accounts = KasAccount::where('aktif', true)->orderBy('id')->get()
            ->map(function ($account) use ($kas, $start, $end) {
                $mutasi = KasMovement::where('kas_account_id', $account->id)
                    ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                    ->get();

                return (object) [
                    'nama' => $account->nama,
                    'saldo_awal' => $kas->saldoPada($account, $start->copy()->subDay()->endOfDay()),
                    'masuk' => (float) $mutasi->sum('debit'),
                    'keluar' => (float) $mutasi->sum('kredit'),
                    'saldo_akhir' => $kas->saldoPada($account, $end),
                ];
            });

        $mutasiPerJenis = KasMovement::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('jenis, COUNT(*) as jumlah, SUM(debit) as masuk, SUM(kredit) as keluar')
            ->groupBy('jenis')
            ->orderBy('jenis')
            ->get();

        return view('kas.laporan', compact(
            'start', 'end', 'pendapatan', 'hpp', 'labaKotor',
            'pengeluarans', 'pengeluaranPerKategori', 'totalPengeluaran', 'labaBersih',
            'accounts', 'mutasiPerJenis'
        ));
    }
}
