<?php $__env->startSection('content'); ?>
    <div class="toolbar">
        <h1>Meja</h1><a class="button" href="<?php echo e(route('mejas.create')); ?>">Tambah</a>
    </div>
    <table>
        <tr>
            <th>Nomor</th>
            <th>Kapasitas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php $__currentLoopData = $mejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($meja->nomor_meja); ?></td>
                <td><?php echo e($meja->kapasitas); ?></td>
                <td><?php echo e($meja->status_aktif ? 'Aktif' : 'Tidak aktif'); ?></td>
                <td><a href="<?php echo e(route('mejas.edit', $meja)); ?>">Edit</a>
                    <form class="inline" method="post" action="<?php echo e(route('mejas.destroy', $meja)); ?>"><?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?><button class="danger">Hapus</button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table><?php echo e($mejas->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/mejas/index.blade.php ENDPATH**/ ?>