<?php
    // DETEKSI ROLE SECARA DINAMIS UNTUK MEMILIH LAYOUT
    $role = auth('karyawan')->user()->role;
    if ($role === 'kasir') {
        $activeLayout = 'layouts.kasir';
    } elseif ($role === 'manager') {
        $activeLayout = 'layouts.manager';
    } else {
        $activeLayout = 'layouts.app';
    }

    $kategoriProduks = \App\Models\KategoriProduk::orderBy('nama_kategori')->get();
?>



<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-bold text-slate-900">Terminal Kasir</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Station #01
                        </span>
                    </div>
                    <p class="text-sm text-slate-500">Pilih menu, atur jumlah, lalu simpan pesanan.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-3 py-2 bg-slate-50 rounded-lg border border-slate-200">
                        <p class="text-[10px] text-slate-500 font-semibold uppercase">Kasir</p>
                        <p class="text-sm font-bold text-slate-900"><?php echo e(auth('karyawan')->user()->nama_karyawan); ?></p>
                    </div>
                    <div class="px-3 py-2 bg-slate-50 rounded-lg border border-slate-200">
                        <p class="text-[10px] text-slate-500 font-semibold uppercase">Waktu</p>
                        <p class="text-sm font-bold text-slate-900"><?php echo e(now()->format('H:i')); ?> WIB</p>
                    </div>
                    <a href="<?php echo e(route('transaksis.index')); ?>" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Riwayat Transaksi
                    </a>
                </div>
            </div>

            <!-- Search & Category Filter -->
            <div class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input id="pos-search" type="search" placeholder="Cari nama menu atau kategori..." class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar">
                    <button class="pos-category-tab active" data-category="all">
                        Semua Menu <span class="ml-1 bg-white/20 px-1.5 py-0.5 rounded-full text-[10px]"><?php echo e($produks->count()); ?></span>
                    </button>
                    <?php $__currentLoopData = $kategoriProduks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button class="pos-category-tab inactive" data-category="<?php echo e(strtolower($kategori->nama_kategori)); ?>">
                            <?php echo e($kategori->nama_kategori); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- KATALOG PRODUK (2/3 lebar) -->
            <section class="lg:col-span-2">
                <?php if($produks->isEmpty()): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada menu aktif</h3>
                        <p class="text-sm text-slate-500">Aktifkan produk bertipe jual untuk menerima pesanan.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" data-pos-products>
                        <?php $__currentLoopData = $produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="pos-product-card" data-pos-product data-search="<?php echo e(strtolower($produk->nama_produk . ' ' . ($produk->kategoriProduk->nama_kategori ?? ''))); ?>" data-category="<?php echo e(strtolower($produk->kategoriProduk->nama_kategori ?? '')); ?>">
                                <?php if($produk->gambar): ?>
                                    <img src="<?php echo e(asset('storage/' . $produk->gambar)); ?>" alt="<?php echo e($produk->nama_produk); ?>" class="pos-product-card-img">
                                <?php else: ?>
                                    <div class="w-full h-32 bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center border-b border-slate-100">
                                        <svg class="w-12 h-12 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-bold text-primary-600 uppercase tracking-wider"><?php echo e($produk->kategoriProduk->nama_kategori ?? 'Menu'); ?></span>
                                        <?php if($produk->stock < 5): ?>
                                            <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Stok: <?php echo e($produk->stock); ?></span>
                                        <?php else: ?>
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Stok: <?php echo e($produk->stock); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <h2 class="font-bold text-slate-900 text-sm mb-1 line-clamp-2 min-h-[2.5rem]"><?php echo e($produk->nama_produk); ?></h2>
                                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                                        <span class="font-bold text-primary-600 text-sm">Rp <?php echo e(number_format($produk->harga_jual, 0, ',', '.')); ?></span>
                                        <button
                                            class="pos-add-btn w-7 h-7 rounded-lg bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                            type="button"
                                            data-add-product
                                            data-id="<?php echo e($produk->id); ?>"
                                            data-name="<?php echo e($produk->nama_produk); ?>"
                                            data-price="<?php echo e((float) $produk->harga_jual); ?>"
                                            data-stock="<?php echo e($produk->stock); ?>"
                                            aria-label="Tambah <?php echo e($produk->nama_produk); ?>"
                                            <?php if($produk->stock < 1): echo 'disabled'; endif; ?>
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="pos-no-results hidden text-center py-12 text-slate-500" data-pos-no-results>Menu tidak ditemukan. Coba kata pencarian lain.</p>
                <?php endif; ?>
            </section>

            <!-- PANEL ORDER / KERANJANG (1/3 lebar) -->
            <aside class="pos-order-panel" id="pos-order" aria-labelledby="pos-order-title">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 id="pos-order-title" class="font-bold text-slate-900">Pesanan Baru</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Periksa item sebelum disimpan</p>
                    </div>
                    <span class="px-2.5 py-1 bg-primary-100 text-primary-700 rounded-lg text-xs font-bold" data-cart-count>0 item</span>
                </div>

                <p class="pos-cart-empty text-center py-12 text-slate-500 text-sm" data-cart-empty>
                    Keranjang kosong.<br>Tambahkan menu dari katalog.
                </p>
                <div class="pos-cart-lines max-h-96 overflow-y-auto p-4" data-cart-lines aria-live="polite"></div>

                <form class="pos-order-form p-4 border-t border-slate-200" id="pos-form" method="post" action="<?php echo e(route('transaksis.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div data-cart-inputs></div>

                    <!-- Tipe Pesanan -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Tipe Pesanan</label>
                        <div class="pos-type-selector">
                            <label class="pos-type-option">
                                <input type="radio" name="tipe_pesanan" value="dine-in" checked>
                                <span>🍽️ Makan di Tempat</span>
                            </label>
                            <label class="pos-type-option">
                                <input type="radio" name="tipe_pesanan" value="take-away">
                                <span>🥡 Bawa Pulang</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pilih Meja -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Pilih Meja <span class="text-red-500">*</span>
                        </label>
                        <select name="meja_id" data-table-select required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Pilih Meja --</option>
                            <?php $__currentLoopData = $mejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($meja->id); ?>">Meja <?php echo e($meja->nomor_meja); ?> · <?php echo e($meja->kapasitas); ?> kursi</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="pos-field-hint text-xs text-slate-500 mt-1.5" data-takeaway-hint hidden>Pesanan bawa pulang tidak memerlukan meja.</p>
                        <?php if($mejas->isEmpty()): ?>
                            <span class="text-xs text-red-600 mt-1.5 block">Tidak ada meja aktif. Aktifkan meja untuk melanjutkan pesanan.</span>
                        <?php endif; ?>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Catatan Pesanan (Opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Contoh: Jangan terlalu manis..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"></textarea>
                    </div>

                    <!-- Ringkasan Total -->
                    <div class="space-y-2 pt-3 border-t border-slate-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Subtotal</span>
                            <strong class="text-slate-900" data-cart-subtotal>Rp 0</strong>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Pajak & Layanan (10%)</span>
                            <strong class="text-slate-900" data-cart-tax>Rp 0</strong>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                            <span class="font-bold text-slate-900">Total Bayar</span>
                            <strong class="text-xl font-bold text-primary-600" data-cart-total>Rp 0</strong>
                        </div>
                    </div>

                    <button class="pos-checkout w-full mt-4 px-4 py-3 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" type="submit" data-checkout disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pesanan
                    </button>
                </form>
            </aside>
        </div>
    </main>
</div>

<script>
    (() => {
        const products = document.querySelector('[data-pos-products]');
        const search = document.querySelector('#pos-search');
        const noResults = document.querySelector('[data-pos-no-results]');
        const cartLines = document.querySelector('[data-cart-lines]');
        const cartEmpty = document.querySelector('[data-cart-empty]');
        const cartInputs = document.querySelector('[data-cart-inputs]');
        const cartCount = document.querySelector('[data-cart-count]');
        const cartSubtotal = document.querySelector('[data-cart-subtotal]');
        const cartTax = document.querySelector('[data-cart-tax]');
        const cartTotal = document.querySelector('[data-cart-total]');
        const tableSelect = document.querySelector('[data-table-select]');
        const orderTypeInputs = document.querySelectorAll('input[name="tipe_pesanan"]');
        const takeawayHint = document.querySelector('[data-takeaway-hint]');
        const checkout = document.querySelector('[data-checkout]');
        const form = document.querySelector('#pos-form');
        const categoryTabs = document.querySelectorAll('.pos-category-tab');
        const cart = new Map();
        let activeCategory = 'all';
        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        function selectedOrderType() {
            return form.querySelector('input[name="tipe_pesanan"]:checked').value;
        }

        function makeButton(label, action, product, ariaLabel) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'pos-qty-btn';
            button.textContent = label;
            button.dataset.cartAction = action;
            button.dataset.id = product.id;
            button.setAttribute('aria-label', ariaLabel);
            return button;
        }

        function renderCart() {
            cartLines.replaceChildren();
            cartInputs.replaceChildren();
            let itemCount = 0;
            let subtotal = 0;
            let index = 0;

            for (const product of cart.values()) {
                itemCount += product.qty;
                subtotal += product.price * product.qty;

                const line = document.createElement('div');
                line.className = 'pos-cart-item';

                const detail = document.createElement('div');
                detail.className = 'flex-1 min-w-0';
                const name = document.createElement('div');
                name.className = 'font-bold text-slate-900 text-sm truncate';
                name.textContent = product.name;
                const price = document.createElement('div');
                price.className = 'text-xs text-slate-500 mt-0.5';
                price.textContent = currency.format(product.price * product.qty);
                detail.append(name, price);

                const customizationLabel = document.createElement('div');
                customizationLabel.className = 'mt-2 pt-2 border-t border-slate-200';
                const customTitle = document.createElement('div');
                customTitle.className = 'text-[10px] font-bold text-slate-500 uppercase mb-1.5';
                customTitle.textContent = 'Kustomisasi';
                customizationLabel.appendChild(customTitle);

                const presetsContainer = document.createElement('div');
                presetsContainer.className = 'grid grid-cols-2 gap-1 mb-2';
                const presets = ['Es sedikit', 'Gula aren dipisah', 'Sirup dipisah', 'Oatmilk (+Rp 8.000)'];

                presets.forEach(preset => {
                    const label = document.createElement('label');
                    label.className = 'pos-customization-preset';

                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.checked = (product.customization || '').includes(preset);

                    const text = document.createTextNode(preset);
                    label.append(input, text);

                    presetsContainer.append(label);
                });

                const customization = document.createElement('textarea');
                customization.className = 'w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs resize-none focus:outline-none focus:ring-1 focus:ring-primary-500';
                customization.name = `items[${index}][customization]`;
                customization.maxLength = 250;
                customization.rows = 2;
                customization.placeholder = 'Catatan tambahan...';

                const currentCustom = product.customization || '';
                const noteOnly = currentCustom.split(', ').filter(p => !presets.includes(p)).join(', ');
                customization.value = noteOnly;

                const updateCustomization = () => {
                    const checked = [];
                    presetsContainer.querySelectorAll('input:checked').forEach(cb => {
                        checked.push(cb.nextSibling.textContent.trim());
                    });
                    const note = customization.value.trim();
                    product.customization = [checked.join(', '), note].filter(Boolean).join(', ');
                };

                presetsContainer.querySelectorAll('input').forEach(cb => {
                    cb.addEventListener('change', updateCustomization);
                });
                customization.addEventListener('input', updateCustomization);

                customizationLabel.append(presetsContainer, customization);

                const controls = document.createElement('div');
                controls.className = 'flex items-center gap-1.5 ml-3';
                controls.append(makeButton('−', 'decrease', product, `Kurangi ${product.name}`));
                const quantity = document.createElement('span');
                quantity.className = 'w-6 text-center font-bold text-sm text-slate-900';
                quantity.textContent = product.qty;
                controls.append(quantity);
                const increase = makeButton('+', 'increase', product, `Tambah ${product.name}`);
                increase.disabled = product.qty >= product.stock;
                controls.append(increase);

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'text-xs text-red-600 hover:text-red-700 font-semibold ml-2';
                remove.dataset.cartAction = 'remove';
                remove.dataset.id = product.id;
                remove.textContent = 'Hapus';

                line.append(detail, controls);
                cartLines.append(line, customizationLabel, remove);

                const productId = document.createElement('input');
                productId.type = 'hidden';
                productId.name = `items[${index}][produk_id]`;
                productId.value = product.id;
                const productQty = document.createElement('input');
                productQty.type = 'hidden';
                productQty.name = `items[${index}][qty]`;
                productQty.value = product.qty;
                cartInputs.append(productId, productQty);
                index += 1;
            }

            cartEmpty.hidden = cart.size > 0;
            cartCount.textContent = `${itemCount} item`;
            const tax = Math.round(subtotal * 0.1);
            cartSubtotal.textContent = currency.format(subtotal);
            cartTax.textContent = currency.format(tax);
            cartTotal.textContent = currency.format(subtotal + tax);
            checkout.disabled = cart.size === 0 || (selectedOrderType() === 'dine-in' && !tableSelect.value);

            document.querySelectorAll('[data-add-product]').forEach((button) => {
                const product = cart.get(button.dataset.id);
                button.disabled = Number(button.dataset.stock) < 1 || (product && product.qty >= product.stock);
            });
        }

        products?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-add-product]');
            if (!button || button.disabled) return;

            const id = button.dataset.id;
            const product = cart.get(id) ?? {
                id,
                name: button.dataset.name,
                price: Number(button.dataset.price),
                stock: Number(button.dataset.stock),
                qty: 0,
                customization: '',
            };
            product.qty = Math.min(product.qty + 1, product.stock);
            cart.set(id, product);
            renderCart();
        });

        cartLines.addEventListener('click', (event) => {
            const button = event.target.closest('[data-cart-action]');
            if (!button) return;

            const product = cart.get(button.dataset.id);
            if (!product) return;
            if (button.dataset.cartAction === 'increase') product.qty = Math.min(product.qty + 1, product.stock);
            if (button.dataset.cartAction === 'decrease') product.qty -= 1;
            if (button.dataset.cartAction === 'remove' || product.qty < 1) cart.delete(product.id);
            else cart.set(product.id, product);
            renderCart();
        });

        search?.addEventListener('input', () => {
            const query = search.value.trim().toLowerCase();
            let visibleCount = 0;
            document.querySelectorAll('[data-pos-product]').forEach((product) => {
                const matchSearch = product.dataset.search.includes(query);
                const matchCategory = activeCategory === 'all' || product.dataset.category === activeCategory;
                product.hidden = !(matchSearch && matchCategory);
                if (!product.hidden) visibleCount += 1;
            });
            if (noResults) noResults.hidden = visibleCount > 0;
        });

        categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                categoryTabs.forEach(t => {
                    t.classList.remove('active');
                    t.classList.add('inactive');
                });
                tab.classList.remove('inactive');
                tab.classList.add('active');
                activeCategory = tab.dataset.category;
                search.dispatchEvent(new Event('input'));
            });
        });

        function syncOrderType() {
            const dineIn = selectedOrderType() === 'dine-in';
            tableSelect.disabled = !dineIn;
            tableSelect.required = dineIn;
            takeawayHint.hidden = dineIn;
            renderCart();
        }

        orderTypeInputs.forEach((input) => input.addEventListener('change', syncOrderType));
        tableSelect.addEventListener('change', renderCart);
        form.addEventListener('submit', (event) => {
            const dineIn = selectedOrderType() === 'dine-in';
            if (cart.size === 0 || (dineIn && !tableSelect.value)) {
                event.preventDefault();
                return;
            }
            checkout.disabled = true;
            checkout.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan pesanan...`;
        });

        syncOrderType();
    })();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make($activeLayout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/transaksis/create.blade.php ENDPATH**/ ?>