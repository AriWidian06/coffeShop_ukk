<?php

namespace App\Http\Controllers;

use App\Models\KasAccount;
use App\Models\Pengeluaran;
use App\Models\Supplier;
use App\Services\KasService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluaran::with(['karyawan', 'supplier', 'account', 'approver'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->kategori))
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = '%'.$request->q.'%';
                $q->where(fn ($inner) => $inner
                    ->where('deskripsi', 'like', $keyword)
                    ->orWhere('catatan', 'like', $keyword));
            })
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('tanggal', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('tanggal', '<=', $request->end_date))
            ->latest('tanggal')
            ->latest('id');

        $pengeluarans = $query->paginate(15)->withQueryString();

        $stats = [
            'pending' => (float) Pengeluaran::pending()->sum('jumlah'),
            'approved' => (float) Pengeluaran::approved()->whereMonth('tanggal', now()->month)->sum('jumlah'),
            'pending_count' => Pengeluaran::pending()->count(),
        ];

        return view('pengeluarans.index', compact('pengeluarans', 'stats'));
    }

    public function create()
    {
        $this->authorize('input-pengeluaran');

        return view('pengeluarans.form', [
            'pengeluaran' => new Pengeluaran(['tanggal' => now()->toDateString(), 'metode' => 'tunai']),
            'suppliers' => Supplier::orderBy('nama_supplier')->get(),
            'accounts' => KasAccount::where('aktif', true)->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('input-pengeluaran');

        $data = $this->validateData($request);
        $data['karyawan_id'] = auth('karyawan')->id();
        $data['kas_account_id'] = $this->accountFor($data['metode'])->id;
        $data['status'] = 'pending';

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('bukti-pengeluaran', 'public');
        }

        Pengeluaran::create($data);

        return redirect()->route('pengeluarans.index')
            ->with('success', 'Pengeluaran diajukan. Menunggu persetujuan manager.');
    }

    public function show(Pengeluaran $pengeluaran)
    {
        $this->authorize('lihat-kas');

        return view('pengeluarans.show', compact('pengeluaran'));
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        $this->authorize('input-pengeluaran');
        $this->ensureEditable($pengeluaran);

        return view('pengeluarans.form', [
            'pengeluaran' => $pengeluaran,
            'suppliers' => Supplier::orderBy('nama_supplier')->get(),
            'accounts' => KasAccount::where('aktif', true)->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $this->authorize('input-pengeluaran');
        $this->ensureEditable($pengeluaran);

        $data = $this->validateData($request);
        $data['kas_account_id'] = $this->accountFor($data['metode'])->id;

        if ($request->hasFile('bukti')) {
            if ($pengeluaran->bukti) {
                Storage::disk('public')->delete($pengeluaran->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('bukti-pengeluaran', 'public');
        }

        $pengeluaran->update($data);

        return redirect()->route('pengeluarans.index')->with('success', 'Pengajuan pengeluaran diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $this->authorize('input-pengeluaran');
        $this->ensureEditable($pengeluaran);

        if ($pengeluaran->bukti) {
            Storage::disk('public')->delete($pengeluaran->bukti);
        }
        $pengeluaran->delete();

        return redirect()->route('pengeluarans.index')->with('success', 'Pengajuan pengeluaran dibatalkan.');
    }

    public function approve(Request $request, Pengeluaran $pengeluaran, KasService $kas)
    {
        $this->authorize('approve-pengeluaran');

        if ($pengeluaran->status !== 'pending') {
            return back()->with('error', 'Pengeluaran ini sudah diproses.');
        }

        DB::transaction(function () use ($request, $pengeluaran, $kas) {
            $pengeluaran->update([
                'status' => 'approved',
                'approved_by' => auth('karyawan')->id(),
                'approved_at' => now(),
                'catatan_approval' => $request->input('catatan_approval'),
            ]);

            $kas->catatPengeluaran($pengeluaran->refresh());
        });

        return back()->with('success', 'Pengeluaran disetujui dan sudah memotong saldo kas.');
    }

    public function reject(Request $request, Pengeluaran $pengeluaran)
    {
        $this->authorize('approve-pengeluaran');

        if ($pengeluaran->status !== 'pending') {
            return back()->with('error', 'Pengeluaran ini sudah diproses.');
        }

        $validated = $request->validate([
            'catatan_approval' => 'required|string|max:500',
        ]);

        $pengeluaran->update([
            'status' => 'rejected',
            'approved_by' => auth('karyawan')->id(),
            'approved_at' => now(),
            'catatan_approval' => $validated['catatan_approval'],
        ]);

        return back()->with('success', 'Pengeluaran ditolak.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'kategori' => 'required|in:bahan_baku,operasional,gaji,lainnya',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'metode' => 'required|in:tunai,transfer',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string|max:1000',
        ]);
    }

    protected function accountFor(string $metode): KasAccount
    {
        return $metode === 'transfer' ? KasAccount::bank() : KasAccount::tunai();
    }

    protected function ensureEditable(Pengeluaran $pengeluaran): void
    {
        if ($pengeluaran->status !== 'pending') {
            abort(403, 'Hanya pengajuan yang masih menunggu persetujuan yang dapat diubah.');
        }
    }
}
