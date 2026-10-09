@extends('layouts.app')
@section('content')
    <style>
        main:has(.pos-page) { max-width: 1480px; padding: 1.25rem clamp(1rem, 2.5vw, 2.25rem); }
        .pos-page { color: #30231b; }
        .pos-heading { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: 1.4rem; }
        .pos-heading h1 { margin: 0; font-size: 1.8rem; }
        .pos-heading p { margin: .35rem 0 0; color: #59483c; }
        .pos-heading a { color: #3b2418; font-weight: 700; }
        .pos-heading-actions { display: flex; align-items: center; gap: 1rem; }
        .pos-workspace { display: grid; grid-template-columns: minmax(0, 1fr) minmax(310px, 370px); align-items: start; gap: 1.25rem; }
        .pos-catalog { min-width: 0; }
        .pos-catalog-tools { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: .85rem; }
        .pos-search { width: min(100%, 430px); min-height: 46px; box-sizing: border-box; padding: .65rem .85rem; border: 1px solid #8b7563; border-radius: 5px; background: #fffdfa; color: #30231b; font: inherit; }
        .pos-menu-count { color: #59483c; font-size: .9rem; white-space: nowrap; }
        .pos-products { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
        .pos-product { display: flex; min-width: 0; min-height: 190px; flex-direction: column; justify-content: space-between; padding: 1rem; border: 1px solid #d8cbbf; border-radius: 5px; background: #fffdfa; }
        .pos-product-category { color: #59483c; font-size: .78rem; }
        .pos-product h2 { margin: .55rem 0 .35rem; font-size: 1.05rem; line-height: 1.3; overflow-wrap: anywhere; }
        .pos-product-stock { margin: 0; color: #59483c; font-size: .84rem; }
        .pos-product-bottom { display: flex; align-items: center; justify-content: space-between; gap: .6rem; margin-top: 1rem; }
        .pos-product-price { font-weight: 700; white-space: nowrap; }
        .pos-add, .pos-checkout { min-height: 44px; padding: .6rem .8rem; border: 1px solid #3b2418; border-radius: 4px; background: #3b2418; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .pos-add { flex: 0 0 auto; }
        .pos-page button:disabled { cursor: not-allowed; opacity: .55; }
        .pos-no-results, .pos-empty-catalog { padding: 2rem 1.25rem; border: 1px solid #d8cbbf; background: #fffdfa; color: #59483c; text-align: center; }
        .pos-order { position: sticky; top: 1rem; border: 1px solid #c9b6a6; border-radius: 5px; background: #fffdfa; }
        .pos-order-header { display: flex; align-items: start; justify-content: space-between; gap: .75rem; padding: 1rem 1rem .85rem; border-bottom: 1px solid #e7ded5; }
        .pos-order-header h2 { margin: 0; font-size: 1.15rem; }
        .pos-order-header p { margin: .25rem 0 0; color: #59483c; font-size: .84rem; }
        .pos-cart-count { color: #443226; font-size: .84rem; font-weight: 700; white-space: nowrap; }
        .pos-cart-empty { margin: 0; padding: 1.5rem 1rem; color: #59483c; font-size: .9rem; text-align: center; }
        .pos-cart-lines { display: grid; gap: .75rem; max-height: 36vh; overflow-y: auto; padding: 1rem; }
        .pos-cart-line { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: .65rem; padding-bottom: .75rem; border-bottom: 1px solid #e7ded5; }
        .pos-cart-line:last-child { padding-bottom: 0; border-bottom: 0; }
        .pos-cart-line-name { display: block; font-weight: 700; overflow-wrap: anywhere; }
        .pos-cart-line-price { display: block; margin-top: .25rem; color: #59483c; font-size: .82rem; }
        .pos-cart-controls { display: flex; align-items: center; gap: .35rem; }
        .pos-qty-button { width: 44px; height: 44px; border: 1px solid #8b7563; border-radius: 4px; background: #fff; color: #30231b; font: inherit; font-weight: 700; cursor: pointer; }
        .pos-qty { min-width: 1.4rem; text-align: center; font-weight: 700; }
        .pos-remove { grid-column: 1 / -1; justify-self: start; min-height: 36px; padding: .35rem .45rem; border: 0; background: transparent; color: #7b3026; font: inherit; text-decoration: underline; cursor: pointer; }
        .pos-order-form { padding: 0 1rem 1rem; }
        .pos-field { display: block; margin: .8rem 0 0; color: #443226; font-size: .85rem; font-weight: 700; }
        .pos-field select { min-height: 44px; margin-top: .35rem; border: 1px solid #8b7563; border-radius: 4px; background: #fff; color: #30231b; font: inherit; }
        .pos-type-field { min-width: 0; padding: 0; border: 0; }
        .pos-order-types { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 4px; margin-top: .35rem; padding: 4px; border: 1px solid #8b7563; border-radius: 4px; background: #eee7df; }
        .pos-order-choice { position: relative; display: flex; min-width: 0; margin: 0; font-size: .85rem; font-weight: 700; }
        .pos-order-choice input { position: absolute; width: 1px; height: 1px; opacity: 0; }
        .pos-order-choice span { display: flex; width: 100%; min-height: 44px; align-items: center; justify-content: center; padding: .4rem; border-radius: 3px; color: #443226; cursor: pointer; text-align: center; }
        .pos-order-choice input:checked + span { background: #3b2418; color: #fff; }
        .pos-order-choice input:focus-visible + span { outline: 3px solid #8b4a20; outline-offset: 2px; }
        .pos-field-hint { margin: .35rem 0 0; color: #59483c; font-size: .78rem; font-weight: 400; }
        .pos-operator { display: flex; justify-content: space-between; gap: .75rem; margin-top: .9rem; padding-top: .8rem; border-top: 1px solid #e7ded5; font-size: .87rem; }
        .pos-operator span { color: #59483c; }
        .pos-total { display: flex; align-items: center; justify-content: space-between; gap: .75rem; margin-top: .85rem; padding-top: .85rem; border-top: 1px solid #e7ded5; font-size: 1rem; }
        .pos-total strong { font-size: 1.2rem; }
        .pos-checkout { width: 100%; margin-top: .85rem; }
        .pos-page :focus-visible { outline: 3px solid #8b4a20; outline-offset: 2px; }
        @media (max-width: 1050px) {
            body:not(.cashier-desktop) .pos-workspace { grid-template-columns: minmax(0, 1fr) 320px; gap: .9rem; }
            body:not(.cashier-desktop) .pos-products { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 760px) {
            body:not(.cashier-desktop) main:has(.pos-page) { padding-top: 1rem; }
            body:not(.cashier-desktop) .pos-heading { align-items: flex-start; flex-direction: column; }
            body:not(.cashier-desktop) .pos-workspace { grid-template-columns: minmax(0, 1fr); }
            body:not(.cashier-desktop) .pos-order { position: static; }
            body:not(.cashier-desktop) .pos-products { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 460px) {
            body:not(.cashier-desktop) .pos-catalog-tools { align-items: stretch; flex-direction: column; }
            body:not(.cashier-desktop) .pos-search { width: 100%; }
            body:not(.cashier-desktop) .pos-products { grid-template-columns: minmax(0, 1fr); }
            body:not(.cashier-desktop) .pos-product { min-height: 150px; }
        }
    </style>

    <section class="pos-page">
        <header class="pos-heading">
            <div>
                <h1>POS</h1>
                <p>Pilih menu, atur jumlah, lalu simpan pesanan.</p>
            </div>
            <div class="pos-heading-actions">
                <a href="#pos-order">Lihat keranjang</a>
                <a href="{{ route('transaksis.index') }}">Data Transaksi</a>
            </div>
        </header>

        <div class="pos-workspace">
            <section class="pos-catalog" aria-label="Katalog menu">
                <div class="pos-catalog-tools">
                    <label class="sr-only" for="pos-search">Cari menu</label>
                    <input class="pos-search" id="pos-search" type="search" placeholder="Cari nama menu atau kategori" autocomplete="off">
                    <span class="pos-menu-count">{{ $produks->count() }} menu aktif</span>
                </div>

                @if ($produks->isEmpty())
                    <div class="pos-empty-catalog">
                        <strong>Belum ada menu aktif</strong>
                        <p>Aktifkan produk bertipe jual untuk menerima pesanan.</p>
                    </div>
                @else
                    <div class="pos-products" data-pos-products>
                        @foreach ($produks as $produk)
                            <article class="pos-product" data-pos-product data-search="{{ strtolower($produk->nama_produk . ' ' . ($produk->kategoriProduk->nama_kategori ?? '')) }}">
                                <div>
                                    <span class="pos-product-category">{{ $produk->kategoriProduk->nama_kategori ?? 'Menu' }}</span>
                                    <h2>{{ $produk->nama_produk }}</h2>
                                    <p class="pos-product-stock">Stok {{ $produk->stock }} {{ $produk->satuan }}</p>
                                </div>
                                <div class="pos-product-bottom">
                                    <span class="pos-product-price">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                                    <button
                                        class="pos-add"
                                        type="button"
                                        data-add-product
                                        data-id="{{ $produk->id }}"
                                        data-name="{{ $produk->nama_produk }}"
                                        data-price="{{ (float) $produk->harga_jual }}"
                                        data-stock="{{ $produk->stock }}"
                                        aria-label="Tambah {{ $produk->nama_produk }}"
                                        @disabled($produk->stock < 1)
                                    >Tambah</button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <p class="pos-no-results" data-pos-no-results hidden>Menu tidak ditemukan. Coba kata pencarian lain.</p>
                @endif
            </section>

            <aside class="pos-order" id="pos-order" aria-labelledby="pos-order-title">
                <div class="pos-order-header">
                    <div>
                        <h2 id="pos-order-title">Pesanan baru</h2>
                        <p>Periksa item sebelum disimpan.</p>
                    </div>
                    <span class="pos-cart-count" data-cart-count>0 item</span>
                </div>

                <p class="pos-cart-empty" data-cart-empty>Keranjang kosong. Tambahkan menu dari katalog.</p>
                <div class="pos-cart-lines" data-cart-lines aria-live="polite"></div>

                <form class="pos-order-form" id="pos-form" method="post" action="{{ route('transaksis.store') }}">
                    @csrf
                    <div data-cart-inputs></div>
                    <fieldset class="pos-field pos-type-field">
                        <legend>Tipe pesanan</legend>
                        <div class="pos-order-types">
                            <label class="pos-order-choice">
                                <input type="radio" name="tipe_pesanan" value="dine-in" checked>
                                <span>Makan di tempat</span>
                            </label>
                            <label class="pos-order-choice">
                                <input type="radio" name="tipe_pesanan" value="take-away">
                                <span>Bawa pulang</span>
                            </label>
                        </div>
                    </fieldset>
                    <label class="pos-field">
                        Meja
                        <select name="meja_id" data-table-select required>
                            <option value="">Pilih meja</option>
                            @foreach ($mejas as $meja)
                                <option value="{{ $meja->id }}">Meja {{ $meja->nomor_meja }} · {{ $meja->kapasitas }} kursi</option>
                            @endforeach
                        </select>
                        <p class="pos-field-hint" data-takeaway-hint hidden>Pesanan bawa pulang tidak memerlukan meja.</p>
                        @if ($mejas->isEmpty())
                            <span class="pos-field-hint">Tidak ada meja aktif. Aktifkan meja untuk melanjutkan pesanan.</span>
                        @endif
                    </label>

                    <div class="pos-operator">
                        <span>Kasir</span>
                        <strong>{{ auth('karyawan')->user()->nama_karyawan }}</strong>
                    </div>
                    <div class="pos-total">
                        <span>Subtotal</span>
                        <strong data-cart-total>Rp 0</strong>
                    </div>
                    <button class="pos-checkout" type="submit" data-checkout disabled>Simpan pesanan</button>
                </form>
            </aside>
        </div>
    </section>

    <script>
        (() => {
            const products = document.querySelector('[data-pos-products]');
            const search = document.querySelector('#pos-search');
            const noResults = document.querySelector('[data-pos-no-results]');
            const cartLines = document.querySelector('[data-cart-lines]');
            const cartEmpty = document.querySelector('[data-cart-empty]');
            const cartInputs = document.querySelector('[data-cart-inputs]');
            const cartCount = document.querySelector('[data-cart-count]');
            const cartTotal = document.querySelector('[data-cart-total]');
            const tableSelect = document.querySelector('[data-table-select]');
            const orderTypeInputs = document.querySelectorAll('input[name="tipe_pesanan"]');
            const takeawayHint = document.querySelector('[data-takeaway-hint]');
            const checkout = document.querySelector('[data-checkout]');
            const form = document.querySelector('#pos-form');
            const cart = new Map();
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
                button.className = 'pos-qty-button';
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
                let total = 0;
                let index = 0;

                for (const product of cart.values()) {
                    itemCount += product.qty;
                    total += product.price * product.qty;

                    const line = document.createElement('div');
                    line.className = 'pos-cart-line';

                    const detail = document.createElement('div');
                    const name = document.createElement('span');
                    name.className = 'pos-cart-line-name';
                    name.textContent = product.name;
                    const price = document.createElement('span');
                    price.className = 'pos-cart-line-price';
                    price.textContent = currency.format(product.price * product.qty);
                    detail.append(name, price);

                    const controls = document.createElement('div');
                    controls.className = 'pos-cart-controls';
                    controls.append(makeButton('-', 'decrease', product, `Kurangi ${product.name}`));
                    const quantity = document.createElement('span');
                    quantity.className = 'pos-qty';
                    quantity.textContent = product.qty;
                    controls.append(quantity);
                    const increase = makeButton('+', 'increase', product, `Tambah ${product.name}`);
                    increase.disabled = product.qty >= product.stock;
                    controls.append(increase);

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'pos-remove';
                    remove.dataset.cartAction = 'remove';
                    remove.dataset.id = product.id;
                    remove.textContent = 'Hapus item';
                    line.append(detail, controls, remove);
                    cartLines.append(line);

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
                cartTotal.textContent = currency.format(total);
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
                    product.hidden = !product.dataset.search.includes(query);
                    if (!product.hidden) visibleCount += 1;
                });
                if (noResults) noResults.hidden = visibleCount > 0;
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
                checkout.textContent = 'Menyimpan pesanan...';
            });

            syncOrderType();
        })();
    </script>
@endsection
