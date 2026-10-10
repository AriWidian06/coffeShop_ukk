<!-- Form Fields -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- Nama Produk -->
    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk *</label>
        <input type="text" name="nama_produk" value="<?php echo e(old('nama_produk', $produk->nama_produk ?? '')); ?>" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="Contoh: Kopi Susu Aren Gula Melaka">
        <?php $__errorArgs = ['nama_produk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Deskripsi -->
    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Produk</label>
        <textarea name="deskripsi" rows="3"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="Deskripsi singkat produk..."><?php echo e(old('deskripsi', $produk->deskripsi ?? '')); ?></textarea>
        <?php $__errorArgs = ['deskripsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Kategori -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Kategori Produk *</label>
        <select name="kategori_produk_id" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
            <option value="">Pilih Kategori</option>
            <?php $__currentLoopData = $kategoriProduks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($kategori->id); ?>" <?php echo e(old('kategori_produk_id', $produk->kategori_produk_id ?? '') == $kategori->id ? 'selected' : ''); ?>>
                    <?php echo e($kategori->nama_kategori); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['kategori_produk_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Supplier -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Supplier *</label>
        <select name="supplier_id" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
            <option value="">Pilih Supplier</option>
            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($supplier->id); ?>" <?php echo e(old('supplier_id', $produk->supplier_id ?? '') == $supplier->id ? 'selected' : ''); ?>>
                    <?php echo e($supplier->nama_supplier); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['supplier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Harga Jual -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Harga Jual (Rp) *</label>
        <input type="number" name="harga_jual" value="<?php echo e(old('harga_jual', $produk->harga_jual ?? '')); ?>" required min="0"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="32000">
        <?php $__errorArgs = ['harga_jual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Harga Beli (HPP) -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Harga Beli / HPP (Rp) *</label>
        <input type="number" name="harga_beli" value="<?php echo e(old('harga_beli', $produk->harga_beli ?? '')); ?>" required min="0"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="17000">
        <?php $__errorArgs = ['harga_beli'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Tipe Produk -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Produk *</label>
        <select name="tipe" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
            <option value="jual" <?php echo e(old('tipe', $produk->tipe ?? '') == 'jual' ? 'selected' : ''); ?>>Siap Jual (Menu)</option>
            <option value="bahan baku" <?php echo e(old('tipe', $produk->tipe ?? '') == 'bahan baku' ? 'selected' : ''); ?>>Bahan Baku</option>
        </select>
        <?php $__errorArgs = ['tipe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Satuan -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Satuan *</label>
        <input type="text" name="satuan" value="<?php echo e(old('satuan', $produk->satuan ?? 'pcs')); ?>" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="pcs, cup, kg, liter">
        <?php $__errorArgs = ['satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Stock -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Stok Awal *</label>
        <input type="number" name="stock" value="<?php echo e(old('stock', $produk->stock ?? 0)); ?>" required min="0"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="100">
        <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Status Aktif -->
    <div class="flex items-center gap-3">
        <input type="checkbox" name="status_aktif" id="status_aktif" value="1" 
            <?php echo e(old('status_aktif', $produk->status_aktif ?? true) ? 'checked' : ''); ?>

            class="w-5 h-5 text-primary-600 rounded border-slate-300 focus:ring-primary-500">
        <label for="status_aktif" class="text-sm font-bold text-slate-700 cursor-pointer">
            Produk Aktif (Tampil di Menu)
        </label>
    </div>
</div><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/produks/form.blade.php ENDPATH**/ ?>