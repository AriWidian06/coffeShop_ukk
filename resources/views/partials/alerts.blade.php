{{--
    Notifikasi global yang selalu tampil di atas konten.

    Menampilkan:
    - semua error validasi Laravel ($errors) sebagai ringkasan yang tetap terlihat;
    - pesan flash session: error, warning, success, dan status.

    Pemakaian:
    @include('partials.alerts')
    @include('partials.alerts', ['class' => 'mb-6', 'showValidationErrors' => false])
--}}
@php
    $showValidationErrors = $showValidationErrors ?? true;
    $alerts = [];

    if ($showValidationErrors && isset($errors) && $errors->any()) {
        $alerts[] = [
            'variant' => 'error',
            'title' => $errors->count() > 1 ? 'Periksa kembali data berikut' : 'Terjadi kesalahan',
            'messages' => $errors->all(),
        ];
    }

    $flashAlerts = [
        'error' => ['variant' => 'error', 'title' => 'Terjadi kesalahan'],
        'warning' => ['variant' => 'warning', 'title' => 'Perhatian'],
        'success' => ['variant' => 'success', 'title' => 'Berhasil'],
        'status' => ['variant' => 'info', 'title' => 'Informasi'],
    ];

    foreach ($flashAlerts as $key => $meta) {
        if (! session()->has($key)) {
            continue;
        }

        $alerts[] = [
            'variant' => $meta['variant'],
            'title' => $meta['title'],
            'messages' => [(string) session($key)],
        ];
    }

    $variants = [
        'error' => [
            'wrapper' => 'border-red-200 bg-red-50',
            'icon' => 'bg-red-100 text-red-600',
            'title' => 'text-red-900',
            'message' => 'text-red-700',
        ],
        'warning' => [
            'wrapper' => 'border-amber-200 bg-amber-50',
            'icon' => 'bg-amber-100 text-amber-600',
            'title' => 'text-amber-900',
            'message' => 'text-amber-700',
        ],
        'success' => [
            'wrapper' => 'border-emerald-200 bg-emerald-50',
            'icon' => 'bg-emerald-100 text-emerald-600',
            'title' => 'text-emerald-900',
            'message' => 'text-emerald-700',
        ],
        'info' => [
            'wrapper' => 'border-blue-200 bg-blue-50',
            'icon' => 'bg-blue-100 text-blue-600',
            'title' => 'text-blue-900',
            'message' => 'text-blue-700',
        ],
    ];
@endphp

@if (count($alerts) > 0)
    <div class="space-y-3 {{ $class ?? 'mx-4 mt-4 sm:mx-6 lg:mx-8 lg:mt-6' }}" data-app-alerts>
        @foreach ($alerts as $alert)
            @php($style = $variants[$alert['variant']] ?? $variants['error'])
            <div role="alert"
                 data-app-alert
                 class="flex items-start gap-3 rounded-2xl border p-3.5 shadow-sm sm:p-4 {{ $style['wrapper'] }}">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $style['icon'] }}">
                    @if ($alert['variant'] === 'success')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif ($alert['variant'] === 'warning')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    @elseif ($alert['variant'] === 'info')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </span>

                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold sm:text-sm {{ $style['title'] }}">{{ $alert['title'] }}</p>

                    @if (count($alert['messages']) === 1)
                        <p class="mt-0.5 text-xs leading-relaxed {{ $style['message'] }}">{{ $alert['messages'][0] }}</p>
                    @else
                        <ul class="mt-1 list-disc space-y-0.5 pl-4 text-xs leading-relaxed {{ $style['message'] }}">
                            @foreach ($alert['messages'] as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <button type="button"
                        onclick="this.closest('[data-app-alert]').remove()"
                        class="shrink-0 rounded-lg p-1 text-slate-400 transition hover:bg-white/70 hover:text-slate-700"
                        aria-label="Tutup notifikasi">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
