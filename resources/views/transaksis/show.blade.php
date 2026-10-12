@php
    // DETEKSI ROLE SECARA DINAMIS UNTUK MEMILIH LAYOUT
    $role = auth('karyawan')->user()->role;
    if ($role === 'kasir') {
        $activeLayout = 'layouts.kasir';
    } elseif ($role === 'manager') {
        $activeLayout = 'layouts.manager';
    } else {
        $activeLayout = 'layouts.app';
    }

    $subtotalItems = (float) $transaksi->detail_transaksis->sum('subtotal');
    $pajakLayanan = max(0, (float) $transaksi->total_harga - $subtotalItems);
    $statusLabels = [
        'pending' => 'Menunggu',
        'ready' => 'Siap',
        'prosesed' => 'Diproses',
        'completed' => 'Selesai',
    ];
    $canProcess = in_array($role, ['kasir', 'admin'], true);
@endphp

@extends($activeLayout)

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('transaksis.index') }}" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition" title="Kembali">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-slate-900">Detail Transaksi</h1>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'ready' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'prosesed' => 'bg-purple-100 text-purple-700 border-purple-200',
                                    'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                ];
                                $statusClass = $statusColors[$transaksi->status_pesanan] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                {{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">ID Transaksi: <span class="font-mono font-bold text-slate-700">#{{ $transaksi->id }}</span></p>
                    </div>
                </div>
                @if ($canProcess)
                    <a href="{{ route('transaksis.receipt', $transaksi) }}" target="_blank" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Struk
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6 max-w-6xl mx-auto space-y-6">
        
        <!-- Info Grid -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Informasi Pesanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-slate-500 font-semibold mb-1">Tipe Pesanan</p>
                    <p class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        @if($transaksi->tipe_pesanan === 'take-away')
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                            Bawa Pulang
                        @else
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Makan di Tempat
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold mb-1">Meja</p>
                    <p class="text-sm font-bold text-slate-900">{{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Tanpa Meja' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold mb-1">Sumber / Kasir</p>
                    <p class="text-sm font-bold text-slate-900">{{ $transaksi->sumber_pesanan === 'web' ? 'Web Kafe' : ($transaksi->karyawan->nama_karyawan ?? '-') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold mb-1">Waktu Transaksi</p>
                    <p class="text-sm font-bold text-slate-900">{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') ?? '-' }}</p>
                </div>
            </div>

            @if ($transaksi->catatan)
                <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-amber-800 uppercase mb-1">Catatan Pesanan</p>
                        <p class="text-sm text-amber-900 whitespace-pre-line">{{ $transaksi->catatan }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Item Pesanan</h2>
                <span class="text-xs font-semibold text-slate-500">{{ $transaksi->detail_transaksis->count() }} baris</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase w-32">Jumlah</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase w-40">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($transaksi->detail_transaksis as $detail)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-900">{{ $detail->produk->nama_produk ?? 'Produk dihapus' }}</p>
                                    @if ($detail->customization)
                                        <span class="inline-block mt-1 px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] rounded border border-slate-200 italic">
                                            {{ $detail->customization }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-semibold text-slate-700">{{ $detail->QTY ?? $detail->qty }}</td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-slate-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">Tidak ada item pada transaksi ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200">
                        <tr>
                            <td colspan="2" class="px-6 py-3 text-right text-sm text-slate-600">Subtotal</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-slate-900">Rp {{ number_format($subtotalItems, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="px-6 py-3 text-right text-sm text-slate-600">Pajak & Layanan</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-slate-900">Rp {{ number_format($pajakLayanan, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-primary-50">
                            <td colspan="2" class="px-6 py-4 text-right text-base font-bold text-primary-800">Total Transaksi</td>
                            <td class="px-6 py-4 text-right text-base font-bold text-primary-700">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Actions Grid: Status & Payment -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Update Status -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Status Pesanan</h2>
                <p class="text-sm text-slate-600 mb-4">Status saat ini: <span class="font-bold text-slate-900">{{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}</span></p>
                
                @if ($canProcess)
                    <form method="post" action="{{ route('transaksis.status', $transaksi) }}">
                        @csrf
                        @method('PATCH')
                        <label class="block text-xs font-bold text-slate-700 mb-2">Ubah Status</label>
                        <div class="flex gap-3">
                            <select name="status_pesanan" required class="flex-1 px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($transaksi->status_pesanan === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20">
                                Perbarui
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-xs text-slate-500 italic">Anda tidak memiliki izin untuk mengubah status pesanan.</p>
                @endif
            </div>

            <!-- Payment Section -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Pembayaran</h2>
                
                @if ($transaksi->pembayaran)
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-emerald-700 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm font-bold">Pembayaran Sudah Diterima</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <p class="text-xs text-slate-500">Jumlah Bayar</p>
                                <p class="text-sm font-bold text-slate-900">Rp {{ number_format($transaksi->pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Metode</p>
                                <p class="text-sm font-bold text-slate-900">{{ strtoupper($transaksi->pembayaran->metode_pembayaran) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Kembalian</p>
                                <p class="text-sm font-bold text-slate-900">Rp {{ number_format($transaksi->pembayaran->kembalian, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Waktu Bayar</p>
                                <p class="text-sm font-bold text-slate-900">{{ $transaksi->pembayaran->waktu_pembayaran?->format('d/m/Y H:i') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @elseif ($canProcess)
                    <p class="text-sm text-slate-600 mb-4">Belum dibayar. Masukkan jumlah yang diterima untuk menyelesaikan pesanan.</p>
                    <form method="post" action="{{ route('pembayarans.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Jumlah Bayar</label>
                            <input type="number" step="0.01" name="jumlah_bayar" min="{{ $transaksi->total_harga }}" required 
                                class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Min. Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Metode Pembayaran</label>
                            <select name="metode_pembayaran" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="cash">Tunai</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold text-sm hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20">
                            Catat Pembayaran
                        </button>
                    </form>
                @else
                    <p class="text-sm text-slate-600 italic">Belum dibayar. Hubungi kasir untuk memproses pembayaran.</p>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection