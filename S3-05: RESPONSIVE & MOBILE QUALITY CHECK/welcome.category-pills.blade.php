<!-- Excerpt dari: resources/views/welcome.blade.php -->
<!-- Fokus: filter kategori horizontal pada mobile. -->

<div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-1">
    <button onclick="setCategory('semua', this)" class="category-btn active px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-all bg-primary-600 text-white shadow-md shadow-primary-600/20">
        Semua
    </button>
    @foreach($kategoriProduks as $kategori)
        @php $categorySlug = \Illuminate\Support\Str::slug($kategori->nama_kategori, '-'); @endphp
        <button onclick="setCategory('{{ $categorySlug }}', this)" class="category-btn px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">
            {{ $kategori->nama_kategori }}
        </button>
    @endforeach
</div>
```