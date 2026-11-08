<!-- Excerpt dari: resources/views/welcome.blade.php -->
<!-- Fokus: layout product card horizontal untuk mobile. -->

<div class="flex gap-3">
    <div class="w-24 h-24 rounded-xl bg-slate-100 overflow-hidden shrink-0 relative shadow-inner">
        <img src="{{ $imageUrl }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover">
        @if($badge)
            <span class="absolute top-1 left-1 px-1.5 py-0.5 {{ $badgeColor }} text-white text-[9px] font-extrabold rounded-md shadow-sm">{{ $badge }}</span>
        @endif
    </div>

    <div class="flex-1 min-w-0 flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-slate-900 text-sm leading-tight mb-1">{{ $produk->nama_produk }}</h3>
            <p class="text-[11px] text-slate-500 italic line-clamp-2 leading-relaxed">{{ $produk->deskripsi ?? 'Produk berkualitas dari Bagi Kopi.' }}</p>
        </div>
        <div class="flex items-center justify-between mt-2">
            <span class="font-extrabold text-primary-600 text-sm">Rp {{ $hargaFormatted }}</span>
            @if($produk->stock > 0)
                <button type="button" onclick="quickAddToCart({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga_jual }}, {{ $produk->stock }})"
                    class="w-8 h-8 rounded-xl bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 active:scale-95 shadow-md shadow-primary-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            @else
                <span class="text-[10px] font-bold px-2 py-1 bg-red-50 text-red-600 rounded-lg">Habis</span>
            @endif
        </div>
    </div>
</div>
```