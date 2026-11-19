<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    public function index(Request $request)
    {
        $area = $request->query('area');
        $query = Meja::orderBy('nomor_meja', 'asc');

        if ($area === 'lantai1') {
            $query->where('nomor_meja', '<=', 10);
        } elseif ($area === 'lantai2') {
            $query->where('nomor_meja', '>', 10);
        }

        $mejas = $query->paginate(12)->withQueryString();
        
        // Hitung statistik dari seluruh data, bukan hanya halaman aktif
        $totalMeja = Meja::count();
        $mejaAktif = Meja::where('status_aktif', true)->count();
        $mejaNonaktif = Meja::where('status_aktif', false)->count();
        $totalKapasitas = Meja::sum('kapasitas');
        $lantai1Count = Meja::where('nomor_meja', '<=', 10)->count();
        $lantai2Count = Meja::where('nomor_meja', '>', 10)->count();
        
        return view('mejas.index', compact(
            'mejas',
            'totalMeja',
            'mejaAktif',
            'mejaNonaktif',
            'totalKapasitas',
            'lantai1Count',
            'lantai2Count'
        ));
    }

    public function create()
    {
        return view('mejas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_meja' => 'required|integer|unique:mejas,nomor_meja',
            'kapasitas' => 'required|integer|min:1',
            'status_aktif' => 'nullable|in:0,1', // Validasi eksplisit 0 atau 1
        ]);

        // PERBAIKAN: Gunakan $request->boolean() untuk mengonversi '0'/'1' menjadi true/false
        Meja::create([
            'nomor_meja' => $validated['nomor_meja'],
            'kapasitas' => $validated['kapasitas'],
            'status_aktif' => $request->boolean('status_aktif'), 
        ]);

        return redirect()->route('mejas.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(Meja $meja)
    {
        return view('mejas.edit', compact('meja'));
    }

    public function update(Request $request, Meja $meja)
    {
        $validated = $request->validate([
            'nomor_meja' => 'required|integer|unique:mejas,nomor_meja,' . $meja->id,
            'kapasitas' => 'required|integer|min:1',
            'status_aktif' => 'required|in:0,1', // Validasi eksplisit 0 atau 1
        ]);

        // PERBAIKAN: Gunakan $request->boolean() agar nilai '0' dari form dibaca sebagai false
        $meja->update([
            'nomor_meja' => $validated['nomor_meja'],
            'kapasitas' => $validated['kapasitas'],
            'status_aktif' => $request->boolean('status_aktif'),
        ]);

        return redirect()->route('mejas.index')->with('success', 'Data meja berhasil diperbarui.');
    }

    public function destroy(Meja $meja)
    {
        // FK transaksis.meja_id memakai cascade; hapus meja yang punya riwayat
        // transaksi akan ikut menghapus laporan penjualan. Nonaktifkan saja.
        if ($meja->transaksis()->exists()) {
            return back()->with('error', 'Meja ini sudah memiliki riwayat transaksi dan tidak dapat dihapus. Nonaktifkan meja sebagai gantinya.');
        }

        $meja->delete();
        return redirect()->route('mejas.index')->with('success', 'Meja berhasil dihapus.');
    }

    /**
     * Buat SVG QR code secara lokal (tanpa GD, Imagick, atau API eksternal).
     */
    private function generateQrSvg(string $data, int $size = 600): string
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size, 4),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $qrCode = \BaconQrCode\Encoder\Encoder::encode($data, \BaconQrCode\Common\ErrorCorrectionLevel::H());

        return $renderer->render($qrCode);
    }

    /**
     * Sajikan gambar QR meja (inline, untuk ditampilkan di halaman).
     */
    public function qrImage(Meja $meja)
    {
        $qrUrl = route('welcome', ['meja_id' => $meja->id]);

        return response($this->generateQrSvg($qrUrl, 300), 200)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Unduh QR code meja sebagai file SVG (bisa dibuka di browser & editor).
     */
    public function downloadQr(Meja $meja)
    {
        $qrUrl = route('welcome', ['meja_id' => $meja->id]);
        $svg = $this->generateQrSvg($qrUrl, 600);
        $filename = 'QR-Meja-' . $meja->nomor_meja . '.svg';

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Unduh semua QR code meja aktif dalam satu file ZIP (SVG).
     */
    public function downloadAllQr()
    {
        $mejas = Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();

        if ($mejas->isEmpty()) {
            return back()->with('error', 'Tidak ada meja aktif untuk diunduh.');
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'qr-meja-') . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($mejas as $meja) {
            $qrUrl = route('welcome', ['meja_id' => $meja->id]);
            $zip->addFromString('QR-Meja-' . $meja->nomor_meja . '.svg', $this->generateQrSvg($qrUrl, 400));
        }
        $zip->close();

        return response()->download($zipPath, 'QR-Semua-Meja.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
}
