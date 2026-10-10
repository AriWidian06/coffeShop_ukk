<?php $__env->startSection('content'); ?>
    <h1>Tambah Kategori</h1>
    <form class="form" method="post" action="<?php echo e(route('kategori-produks.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('kategori_produks.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="margin-top: 1.5rem;">
            <button type="submit">Simpan Kategori</button>
            <a href="<?php echo e(route('kategori_produks.index')); ?>" style="margin-left: 1rem; color: #666; text-decoration: none; font-size: 0.9rem;">Batal</a>
        </div}
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/kategori_produks/create.blade.php ENDPATH**/ ?>