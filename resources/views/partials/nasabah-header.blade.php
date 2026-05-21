@php
    $title = $title ?? 'Dashboard';
    $subtitle = $subtitle ?? null;
    $showMeta = $showMeta ?? true;
    $actionUrl = $actionUrl ?? null;
    $actionLabel = $actionLabel ?? 'Kembali';
    $nasabahName = $nasabahName ?? ($user_name ?? session('nama_nasabah') ?? session('username') ?? 'Nasabah');
    $nasabahEmail = $nasabahEmail ?? session('email');
    $wibNow = \Carbon\Carbon::now('Asia/Jakarta')->locale('id');
@endphp

<div class="page-header nasabah-page-header">
    <div class="header-content page-intro">
        <h1>{{ $title }}</h1>
        @if (!empty($subtitle))
            <p class="subtle">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($showMeta || $actionUrl)
        <div class="nasabah-header-side">
            @if ($showMeta)
                <div class="nasabah-status" aria-label="Informasi akun nasabah">
                    <span>Nasabah</span>
                    <span>{{ $nasabahName }}</span>
                    @if (!empty($nasabahEmail))
                        <span>{{ $nasabahEmail }}</span>
                    @endif
                    <span>{{ $wibNow->translatedFormat('d M Y H:i') }} WIB</span>
                </div>
            @endif

            @if ($actionUrl)
                <a href="{{ $actionUrl }}" class="back-btn">{{ $actionLabel }}</a>
            @endif
        </div>
    @endif
</div>
