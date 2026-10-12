<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Operasional Outlet / <span class="text-primary-600">Layanan Mandiri</span>
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">Pengaturan Meja & QR Code Self-Order</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Kelola nomor meja, area penempatan, status aktif pemesanan, dan cetak QR code pelanggan secara terintegrasi dengan POS kasir.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Unduh Semua QR (ZIP/PDF)
                    </button>
                    <a href="<?php echo e(route('mejas.create')); ?>" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Meja Baru
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Meja -->
                <div class="stat-card-meja blue">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Meja Terdaftar</p>
                            <h3 class="text-3xl font-bold text-slate-900"><?php echo e($mejas->total()); ?></h3>
                            <p class="text-xs text-primary-700 mt-1 font-semibold">100% Terpasang</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Sedang Digunakan -->
                <div class="stat-card-meja">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Sedang Digunakan</p>
                            <h3 class="text-3xl font-bold text-slate-900"><?php echo e($mejas->where('status_aktif', true)->count()); ?> <span class="text-sm font-normal text-slate-500">Meja</span></h3>
                            <p class="text-xs text-slate-500 mt-1">58% dari total</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Siap Pakai -->
                <div class="stat-card-meja green">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider mb-1">Siap Pakai</p>
                            <h3 class="text-3xl font-bold text-slate-900"><?php echo e($mejas->where('status_aktif', true)->count()); ?> <span class="text-sm font-normal text-slate-500">Meja</span></h3>
                            <p class="text-xs text-emerald-700 mt-1 font-semibold">Standby</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Nonaktif -->
                <div class="stat-card-meja red">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-red-600 font-bold uppercase tracking-wider mb-1">Nonaktif / Servis</p>
                            <h3 class="text-3xl font-bold text-slate-900"><?php echo e($mejas->where('status_aktif', false)->count()); ?> <span class="text-sm font-normal text-slate-500">Meja</span></h3>
                            <p class="text-xs text-red-700 mt-1 font-semibold">Reserved</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex items-center gap-3 mt-6 pb-4 border-b border-slate-200">
                <button class="filter-area-pill active">
                    Semua Area <span class="ml-1 bg-white/20 px-2 py-0.5 rounded-full"><?php echo e($mejas->total()); ?></span>
                </button>
                <button class="filter-area-pill inactive">
                    Lantai 1 Indoor <span class="ml-1 bg-slate-200 px-2 py-0.5 rounded-full">10</span>
                </button>
                <button class="filter-area-pill inactive">
                    Lantai 2 Slow Bar <span class="ml-1 bg-slate-200 px-2 py-0.5 rounded-full">8</span>
                </button>
                <button class="filter-area-pill inactive">
                    Outdoor Terrace <span class="ml-1 bg-slate-200 px-2 py-0.5 rounded-full">6</span>
                </button>
                
                <div class="ml-auto flex items-center gap-2">
                    <select class="px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option>Semua Status Order</option>
                        <option>Sedang Digunakan</option>
                        <option>Standby</option>
                        <option>Reserved</option>
                    </select>
                    <button class="px-3 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
                        Rotasi Token
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT - Grid Meja -->
    <main class="p-6">
        <div class="meja-grid">
            <?php $__empty_1 = true; $__currentLoopData = $mejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="meja-card <?php echo e($meja->status_aktif ? 'active' : 'inactive'); ?>">
                    <!-- Header Meja -->
                    <div class="meja-header">
                        <div class="flex items-center gap-2">
                            <span class="meja-number">Meja #<?php echo e(str_pad($meja->nomor_meja, 2, '0', STR_PAD_LEFT)); ?></span>
                            <span class="text-xs text-slate-500 font-semibold">Lt. <?php echo e($meja->nomor_meja <= 10 ? '1' : '2'); ?></span>
                        </div>
                        
                        <!-- Toggle Switch -->
                        <form method="post" action="<?php echo e(route('mejas.update', $meja)); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="nomor_meja" value="<?php echo e($meja->nomor_meja); ?>">
                            <input type="hidden" name="kapasitas" value="<?php echo e($meja->kapasitas); ?>">
                            <input type="hidden" name="status_aktif" value="<?php echo e($meja->status_aktif ? 0 : 1); ?>">
                            
                            <button type="submit" class="toggle-switch-modern <?php echo e($meja->status_aktif ? 'enabled' : 'disabled'); ?>" title="<?php echo e($meja->status_aktif ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                            </button>
                        </form>
                    </div>

                    <!-- Info Kapasitas & Status -->
                    <div class="mb-4 space-y-2">
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Kapasitas: <strong><?php echo e($meja->kapasitas); ?> Orang</strong></span>
                            <span class="text-slate-400">•</span>
                            <span>Non-Smoking</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <?php if($meja->status_aktif): ?>
                                <span class="meja-status available">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                                    Meja Kosong (Siap Pakai)
                                </span>
                                <span class="text-xs text-slate-500">Standby</span>
                            <?php else: ?>
                                <span class="meja-status inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-1.5"></span>
                                    Nonaktif
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="qr-code-container">
                        <div class="qr-code">
                            <!-- Generate QR Code sederhana menggunakan API atau library -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo e(route('welcome', ['meja_id' => $meja->id])); ?>" 
                                 alt="QR Code Meja <?php echo e($meja->nomor_meja); ?>" 
                                 class="w-full h-full object-contain">
                        </div>
                        <p class="text-xs text-slate-500 font-mono mt-2">TOKEN: #TK-<?php echo e(str_pad($meja->nomor_meja, 2, '0', STR_PAD_LEFT)); ?>-<?php echo e(rand(1000, 9999)); ?></p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-200">
                        <a href="<?php echo e(route('mejas.edit', $meja)); ?>" class="flex-1 px-3 py-2 bg-primary-50 text-primary-700 rounded-lg text-xs font-bold hover:bg-primary-100 transition text-center">
                            Edit Meja
                        </a>
                        <form method="post" action="<?php echo e(route('mejas.destroy', $meja)); ?>" class="inline" onsubmit="return confirm('Yakin ingin menghapus meja ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center py-16">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <p class="font-bold text-slate-900 mb-1">Belum ada meja terdaftar</p>
                    <p class="text-sm text-slate-500 mb-4">Mulai tambahkan meja pertama Anda untuk sistem self-order</p>
                    <a href="<?php echo e(route('mejas.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Meja Pertama
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($mejas->hasPages()): ?>
            <div class="mt-8 flex items-center justify-center">
                <?php echo e($mejas->links()); ?>

            </div>
        <?php endif; ?>
    </main>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/codex/Projects/coffeShop_ukk/resources/views/mejas/index.blade.php ENDPATH**/ ?>