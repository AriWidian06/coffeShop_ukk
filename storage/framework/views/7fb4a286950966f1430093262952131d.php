<?php $__env->startSection('content'); ?>
    <div class="toolbar">
        <h1>Karyawan</h1><a class="button" href="<?php echo e(route('karyawans.create')); ?>">Tambah</a>
    </div>
    <table>
        <tr>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Username</th>
            <th>Aksi</th>
        </tr>
        <?php $__currentLoopData = $karyawans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $karyawan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($karyawan->nama_karyawan); ?></td>
                <td><?php echo e($karyawan->jabatan); ?></td>
                <td><?php echo e($karyawan->username); ?></td>
                <td><a href="<?php echo e(route('karyawans.edit', $karyawan)); ?>">Edit</a>
                    <form class="inline" method="post" action="<?php echo e(route('karyawans.destroy', $karyawan)); ?>"><?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?><button class="danger">Hapus</button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <?php echo e($karyawans->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/karyawans/index.blade.php ENDPATH**/ ?>