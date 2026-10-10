<?php $__env->startSection('content'); ?>
    <div class="toolbar">
        <h1>Supplier</h1><a class="button" href="<?php echo e(route('suppliers.create')); ?>">Tambah</a>
    </div>
    <table>
        <tr>
            <th>Nama</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($supplier->nama_supplier); ?></td>
                <td><?php echo e($supplier->no_telp); ?></td>
                <td><?php echo e($supplier->alamat); ?></td>
                <td><a href="<?php echo e(route('suppliers.edit', $supplier)); ?>">Edit</a>
                    <form class="inline" method="post" action="<?php echo e(route('suppliers.destroy', $supplier)); ?>"><?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?><button class="danger">Hapus</button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table><?php echo e($suppliers->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/suppliers/index.blade.php ENDPATH**/ ?>