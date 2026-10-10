<?php $__env->startSection('content'); ?>
    <div class="toolbar">
        <h1>Kategori Produk</h1><a class="button" href="<?php echo e(route('kategori-produks.create')); ?>">Tambah</a>
    </div>
    <table>
        <tr>
            <th>Nama</th>
            <th>Jumlah produk</th>
            <th>Aksi</th>
        </tr>
        <?php $__currentLoopData = $kategoriProduks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($kategori->nama_kategori); ?></td>
                <td><?php echo e($kategori->produks_count); ?></td>
                <td><a href="<?php echo e(route('kategori-produks.edit', $kategori)); ?>">Edit</a>
                    <form class="inline" method="post" action="<?php echo e(route('kategori-produks.destroy', $kategori)); ?>"><?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?><button class="danger">Hapus</button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table><?php echo e($kategoriProduks->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/kategori_produks/index.blade.php ENDPATH**/ ?>