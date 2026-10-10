<?php $__env->startSection('content'); ?>
<h1>Masuk ke Perkoci Eatery</h1>
<form class="form" method="post" action="<?php echo e(route('login.store')); ?>"><?php echo csrf_field(); ?>
    <label>Username<input name="username" value="<?php echo e(old('username')); ?>" required autofocus></label>
    <label>Password<input type="password" name="password" required></label>
    <p><button type="submit">Masuk</button></p>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/auth/login.blade.php ENDPATH**/ ?>