@php
    $googleClientId = config('services.google.client_id');
    $buttonId = $buttonId ?? 'google-signin-button';
    $errorId = $errorId ?? 'google-auth-error';
    $buttonText = $buttonText ?? 'continue_with';
@endphp

<div class="social-auth">
    <div class="social-divider">
        <span>atau</span>
    </div>

    @if ($googleClientId)
        <div id="{{ $buttonId }}" class="google-signin-button"></div>
        <p id="{{ $errorId }}" class="google-auth-error" hidden></p>
    @else
        <p class="google-auth-disabled">Google SSO belum dikonfigurasi.</p>
    @endif
</div>

@once
    <style>
        .social-auth {
            margin-top: 18px;
        }

        .social-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .social-divider::before,
        .social-divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #e2e8f0;
        }

        .google-signin-button {
            display: flex;
            justify-content: center;
            min-height: 44px;
        }

        .google-auth-error,
        .google-auth-disabled {
            margin-top: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 600;
        }

        .google-auth-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .google-auth-disabled {
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }
    </style>

    @if ($googleClientId)
        <script src="https://accounts.google.com/gsi/client" async defer></script>
    @endif
@endonce

@if ($googleClientId)
    <script>
        (() => {
            const buttonId = @json($buttonId);
            const errorId = @json($errorId);
            const buttonText = @json($buttonText);
            const clientId = @json($googleClientId);
            const endpoint = @json(route('nasabah.google.authenticate'));
            const csrfToken = @json(csrf_token());
            let initialized = false;
            let submitting = false;

            function showGoogleError(message) {
                const errorElement = document.getElementById(errorId);
                if (!errorElement) {
                    return;
                }

                errorElement.textContent = message;
                errorElement.hidden = false;
            }

            async function handleGoogleCredential(response) {
                if (submitting) {
                    return;
                }

                submitting = true;

                try {
                    const request = await fetch(endpoint, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            credential: response.credential,
                        }),
                    });

                    const payload = await request.json().catch(() => ({}));

                    if (!request.ok) {
                        throw new Error(payload.message || 'Login Google gagal diproses.');
                    }

                    window.location.href = payload.redirect || @json(route('nasabah.dashboard'));
                } catch (error) {
                    showGoogleError(error.message || 'Login Google gagal diproses.');
                } finally {
                    submitting = false;
                }
            }

            function initializeGoogleButton(attempt = 0) {
                if (initialized) {
                    return;
                }

                const button = document.getElementById(buttonId);

                if (!button) {
                    return;
                }

                if (!window.google?.accounts?.id) {
                    if (attempt < 20) {
                        window.setTimeout(() => initializeGoogleButton(attempt + 1), 250);
                    } else {
                        showGoogleError('Komponen Google SSO gagal dimuat.');
                    }

                    return;
                }

                google.accounts.id.initialize({
                    client_id: clientId,
                    callback: handleGoogleCredential,
                    auto_select: false,
                });

                google.accounts.id.renderButton(button, {
                    type: 'standard',
                    theme: 'outline',
                    size: 'large',
                    text: buttonText,
                    shape: 'pill',
                    logo_alignment: 'left',
                    width: Math.min(button.parentElement?.clientWidth || 360, 360),
                    locale: 'id',
                });

                initialized = true;
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => initializeGoogleButton());
            } else {
                initializeGoogleButton();
            }
        })();
    </script>
@endif
