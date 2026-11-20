{{-- Notifikasi global: dipakai untuk pesan sukses/gagal setelah aksi. --}}
<div id="app-toast"
     role="status"
     aria-live="polite"
     class="fixed bottom-6 right-6 z-50 transform transition-all duration-300 translate-y-20 opacity-0 pointer-events-none bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-emerald-500/50 text-xs font-bold max-w-sm">
    <div id="app-toast-icon">
        <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
    </div>
    <div id="app-toast-msg" class="flex-1"></div>
    <button type="button"
            onclick="window.hideAppToast()"
            aria-label="Tutup notifikasi"
            class="text-slate-400 hover:text-white text-base leading-none">
        &times;
    </button>
</div>

<script>
    window.showAppToast = function (message, type = 'success') {
        const toast = document.getElementById('app-toast');
        const icon = document.getElementById('app-toast-icon');
        const msg = document.getElementById('app-toast-msg');
        if (!toast || !icon || !msg) return;

        msg.textContent = message;

        if (type === 'error') {
            toast.className = 'fixed bottom-6 right-6 z-50 transform transition-all duration-300 bg-red-950 text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-red-500/50 text-xs font-bold max-w-sm';
            icon.innerHTML = '<div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>';
        } else {
            toast.className = 'fixed bottom-6 right-6 z-50 transform transition-all duration-300 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-emerald-500/50 text-xs font-bold max-w-sm';
            icon.innerHTML = '<div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>';
        }

        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');

        clearTimeout(window.__appToastTimer);
        window.__appToastTimer = setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
        }, 3500);
    };

    window.hideAppToast = function () {
        const toast = document.getElementById('app-toast');
        if (!toast) return;

        toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
    };

    // Pesan flash server (success/error/warning/status) ditampilkan oleh
    // partials.alerts agar tetap terlihat sampai ditutup pengguna.
</script>
