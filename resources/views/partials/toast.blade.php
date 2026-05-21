@php
    $toastType = $type ?? 'success';
    $toastMessages = $messages ?? ($message ?? '');

    if ($toastMessages instanceof \Illuminate\Support\MessageBag) {
        $toastMessages = $toastMessages->all();
    }

    if (!is_array($toastMessages)) {
        $toastMessages = [$toastMessages];
    }

    $toastMessages = array_values(array_filter($toastMessages, function ($item) {
        return trim((string) $item) !== '';
    }));

    $toastId = $id ?? 'gp-toast-' . $toastType . '-' . uniqid();
@endphp

@if (count($toastMessages) > 0)
    <div id="{{ $toastId }}" class="gp-toast gp-toast-{{ $toastType }}" role="alert">
        <div class="gp-toast-icon" aria-hidden="true">
            @if ($toastType === 'danger')
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M6 18 17.94 6M18 18 6.06 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            @elseif ($toastType === 'warning')
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            @else
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M5 11.917 9.724 16.5 19 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            @endif
            <span class="visually-hidden">{{ ucfirst($toastType) }} icon</span>
        </div>

        <div class="gp-toast-message">
            @foreach ($toastMessages as $toastMessage)
                <div>{{ $toastMessage }}</div>
            @endforeach
        </div>

        <button type="button" class="gp-toast-close" data-dismiss-target="#{{ $toastId }}" aria-label="Close">
            <span class="visually-hidden">Close</span>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 18 17.94 6M18 18 6.06 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
        </button>
    </div>
@endif
