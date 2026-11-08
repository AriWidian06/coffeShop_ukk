/*
Excerpt / implementasi opsional:
Anti-spam double click untuk form submit.

Cara pakai:
1. Tambahkan class `js-submit-lock` pada form yang ingin dikunci.
2. Tambahkan `data-btn-text` pada button submit (opsional) untuk teks proses.
3. Script akan menonaktifkan tombol setelah submit pertama dan menampilkan spinner.
*/

(function () {
    function lockSubmitForm(form) {
        if (form.dataset.submitted === 'true') {
            return false;
        }

        const button = form.querySelector('button[type="submit"], input[type="submit"]');
        const text = button ? button.querySelector('[data-btn-text]') : null;
        const spinner = button ? button.querySelector('[data-btn-spinner]') : null;

        form.dataset.submitted = 'true';

        if (button) {
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');
        }

        if (text) {
            text.classList.add('hidden');
        }

        if (spinner) {
            spinner.classList.remove('hidden');
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.js-submit-lock').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!lockSubmitForm(form)) {
                    event.preventDefault();
                }
            }, { once: false });
        });
    });

    // Contoh helper manual untuk form dynamic (misal kasir):
    window.lockPosSubmitButton = function (button) {
        if (!button || button.disabled) {
            return false;
        }

        button.disabled = true;
        button.classList.add('opacity-75', 'cursor-not-allowed');

        return true;
    };
})();
```