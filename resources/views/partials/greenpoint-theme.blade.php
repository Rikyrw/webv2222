<style>
    :root {
        --gp-green: #2f5f3e;
        --gp-green-dark: #244c32;
        --gp-green-soft: #edf5ef;
        --gp-bg: #f5f6f5;
        --gp-card: #ffffff;
        --gp-border: #dfe7e1;
        --gp-text: #17231b;
        --gp-muted: #6d7a71;
        --gp-danger: #dc3545;
        --gp-warning: #b7791f;
        --gp-radius: 10px;
        --gp-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }

    body {
        background: var(--gp-bg) !important;
        color: var(--gp-text);
        font-size: 14px;
    }

    .visually-hidden {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    a {
        color: var(--gp-green);
    }

    a:hover {
        color: var(--gp-green-dark);
    }

    .gp-page,
    .page-shell {
        display: grid;
        gap: 24px;
    }

    .gp-card,
    .card,
    .report-card,
    .dashboard-panel {
        background: var(--gp-card) !important;
        border: 1px solid var(--gp-border) !important;
        border-radius: var(--gp-radius) !important;
        box-shadow: var(--gp-shadow) !important;
    }

    .card-body,
    .gp-card-body {
        padding: 22px !important;
    }

    .gp-card-header,
    .section-header,
    .table-header,
    .card-title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .gp-title,
    .section-title,
    .card-title,
    .report-card-title {
        color: var(--gp-green) !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        line-height: 1.25;
        margin: 0;
        letter-spacing: 0;
    }

    .gp-subtitle,
    .section-subtitle,
    .card-text,
    .report-card-subtitle,
    .text-muted {
        color: var(--gp-muted) !important;
        font-size: 13px;
        line-height: 1.45;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 34px;
        border-radius: 6px !important;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        box-shadow: none !important;
    }

    .btn-sm {
        min-height: 30px;
        padding: 6px 10px;
        font-size: 11px;
    }

    .btn-primary,
    .btn-success {
        --bs-btn-bg: var(--gp-green);
        --bs-btn-border-color: var(--gp-green);
        --bs-btn-hover-bg: var(--gp-green-dark);
        --bs-btn-hover-border-color: var(--gp-green-dark);
        --bs-btn-active-bg: var(--gp-green-dark);
        --bs-btn-active-border-color: var(--gp-green-dark);
        background: var(--gp-green) !important;
        border-color: var(--gp-green) !important;
        color: #ffffff !important;
    }

    .btn-secondary,
    .btn-outline-secondary {
        background: #f4f6f4 !important;
        border-color: #d8e1da !important;
        color: #33443a !important;
    }

    .btn-info,
    .btn-outline-primary {
        background: #eef6f0 !important;
        border-color: #c9ddcf !important;
        color: var(--gp-green) !important;
    }

    .btn-danger,
    .btn-outline-danger {
        background: var(--gp-danger) !important;
        border-color: var(--gp-danger) !important;
        color: #ffffff !important;
    }

    .form-label,
    label {
        color: #35473c;
        font-size: 13px;
        font-weight: 700;
    }

    .form-control,
    .form-select,
    .input,
    .select,
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="number"],
    input[type="date"],
    textarea,
    select {
        border: 1px solid #d8e1da !important;
        border-radius: 7px !important;
        background-color: #ffffff !important;
        color: var(--gp-text);
        font-size: 13px !important;
        box-shadow: none !important;
    }

    .form-control,
    .form-select,
    .input,
    .select,
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="number"],
    input[type="date"],
    select {
        min-height: 38px;
        padding: 8px 11px !important;
    }

    textarea,
    textarea.form-control {
        min-height: 92px;
        padding: 10px 11px !important;
    }

    .form-control:focus,
    .form-select:focus,
    .input:focus,
    .select:focus,
    input:focus,
    textarea:focus,
    select:focus {
        border-color: var(--gp-green) !important;
        box-shadow: 0 0 0 3px rgba(47, 95, 62, 0.12) !important;
        outline: none !important;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: auto;
    }

    .table,
    .gp-table,
    .table-grid,
    .transactions-table,
    table[role="table"] {
        width: 100%;
        border-collapse: collapse !important;
        border: 0 !important;
        margin-bottom: 0;
        font-size: 13px;
        color: #17231b;
    }

    .table thead th,
    .gp-table thead th,
    .table-grid thead th,
    .transactions-table thead th,
    table[role="table"] thead th {
        background: #ffffff !important;
        color: var(--gp-green) !important;
        border: 0 !important;
        border-bottom: 1px solid var(--gp-border) !important;
        padding: 12px 14px !important;
        font-size: 12px;
        font-weight: 800;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table tbody td,
    .gp-table tbody td,
    .table-grid tbody td,
    .transactions-table tbody td,
    table[role="table"] tbody td {
        border: 0 !important;
        border-bottom: 1px solid #e8eee9 !important;
        padding: 12px 14px !important;
        vertical-align: middle;
    }

    .table-striped > tbody > tr:nth-of-type(odd) > * {
        --bs-table-bg-type: #fbfcfb;
    }

    .gp-actions,
    td .d-flex,
    .table-actions {
        gap: 7px !important;
    }

    .badge {
        border-radius: 999px !important;
        padding: 5px 9px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        line-height: 1;
    }

    .badge.bg-success,
    .bg-success.badge {
        background: #def7e5 !important;
        color: #1c6b37 !important;
    }

    .badge.bg-danger,
    .bg-danger.badge {
        background: #fde2e2 !important;
        color: #a61b1b !important;
    }

    .badge.bg-warning,
    .bg-warning.badge {
        background: #fff3cd !important;
        color: #8a5a00 !important;
    }

    .badge.bg-secondary,
    .bg-secondary.badge {
        background: #edf1ee !important;
        color: #526158 !important;
    }

    .alert,
    .error-message {
        border-radius: 8px !important;
        padding: 12px 14px !important;
        font-size: 13px !important;
        font-weight: 700;
        box-shadow: none !important;
    }

    .alert-success,
    .alert.alert-success {
        background: #effaf2 !important;
        border: 1px solid #c9e8d1 !important;
        color: #1c6b37 !important;
    }

    .alert-danger,
    .alert-error,
    .error-message {
        background: #fff1f1 !important;
        border: 1px solid #f2c8c8 !important;
        color: #9f1d1d !important;
    }

    .gp-toast {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 420px;
        min-height: 58px;
        margin: 0 0 14px;
        padding: 14px;
        color: var(--gp-muted);
        background: #ffffff;
        border: 1px solid var(--gp-border);
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .gp-toast-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        border-radius: 7px;
    }

    .gp-toast-icon svg,
    .gp-toast-close svg {
        width: 20px;
        height: 20px;
    }

    .gp-toast-success .gp-toast-icon {
        color: #1c6b37;
        background: #def7e5;
    }

    .gp-toast-danger .gp-toast-icon {
        color: #a61b1b;
        background: #fde2e2;
    }

    .gp-toast-warning .gp-toast-icon {
        color: #8a5a00;
        background: #fff3cd;
    }

    .gp-toast-message {
        margin-left: 12px;
        color: #526158;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.45;
    }

    .gp-toast-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin-left: auto;
        flex: 0 0 32px;
        color: #6d7a71;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 7px;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .gp-toast-close:hover,
    .gp-toast-close:focus {
        color: var(--gp-text);
        background: #edf1ee;
        outline: none;
    }

    .gp-toast-close:focus {
        box-shadow: 0 0 0 3px rgba(47, 95, 62, 0.12);
    }

    .gp-date-range {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: nowrap;
        width: min(100%, 620px);
    }

    .gp-date-field {
        position: relative;
        flex: 1 1 0;
        min-width: 0;
    }

    .gp-date-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        color: var(--gp-muted);
        pointer-events: none;
        transform: translateY(-50%);
        z-index: 1;
    }

    .gp-date-icon svg {
        width: 18px;
        height: 18px;
    }

    .gp-date-range .date-input,
    .gp-date-range input[type="date"] {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px 9px 40px !important;
        background: #f4f6f4 !important;
        border: 1px solid #d8e1da !important;
        border-radius: 7px !important;
        color: var(--gp-text);
        font-size: 13px !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        color-scheme: light;
    }

    .gp-date-range .date-input:focus,
    .gp-date-range input[type="date"]:focus {
        border-color: var(--gp-green) !important;
        box-shadow: 0 0 0 3px rgba(47, 95, 62, 0.12) !important;
        outline: none !important;
    }

    .gp-date-separator {
        color: var(--gp-muted);
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        flex: 0 0 auto;
    }

    .filter-group.gp-date-filter {
        flex: 1 1 620px;
        min-width: min(100%, 620px);
        max-width: 620px;
    }

    .gp-toast-stack {
        position: fixed;
        top: 18px;
        right: 18px;
        z-index: 11000;
        display: grid;
        gap: 10px;
        width: min(420px, calc(100vw - 32px));
        pointer-events: none;
    }

    .gp-toast-stack .gp-toast {
        margin: 0;
        pointer-events: auto;
    }

    .modal-content {
        border: 1px solid var(--gp-border) !important;
        border-radius: 10px !important;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.18) !important;
    }

    .modal-header {
        background: #ffffff !important;
        color: var(--gp-green) !important;
        border-bottom: 1px solid var(--gp-border) !important;
    }

    .modal-title {
        color: var(--gp-green);
        font-size: 16px;
        font-weight: 800;
    }

    .modal-footer {
        border-top: 1px solid var(--gp-border) !important;
        background: #fbfcfb;
    }

    .gp-empty,
    .empty-state {
        padding: 36px 18px;
        color: var(--gp-muted);
        text-align: center;
        font-size: 13px;
        font-weight: 600;
    }

    .dataTables_wrapper,
    .dt-container {
        color: var(--gp-text);
        font-size: 13px;
    }

    .dt-search,
    .dt-length,
    .dt-info,
    .dt-paging {
        color: var(--gp-muted) !important;
        font-size: 12px !important;
    }

    .dt-search input,
    .dt-length select {
        margin-left: 8px !important;
    }

    .dt-paging .dt-paging-button,
    .page-link {
        border-radius: 6px !important;
        border-color: #d8e1da !important;
        color: var(--gp-green) !important;
    }

    .dt-paging .current,
    .page-item.active .page-link {
        background: var(--gp-green) !important;
        border-color: var(--gp-green) !important;
        color: #ffffff !important;
    }

    .login-container,
    .register-container,
    body > main.card,
    .shell .card {
        border-radius: 12px !important;
        border: 1px solid var(--gp-border) !important;
        box-shadow: var(--gp-shadow) !important;
        background: #ffffff !important;
        backdrop-filter: none !important;
    }

    .login-container::before,
    .register-container::before {
        content: "" !important;
        width: 54px !important;
        height: 54px !important;
        margin: 24px auto 16px !important;
        border-radius: 12px !important;
        background: #ffffff url("{{ asset('images/logo.png') }}") center / 34px 34px no-repeat !important;
        border: 1px solid var(--gp-border);
        box-shadow: none !important;
    }

    .brand-mark {
        background: #ffffff !important;
        color: var(--gp-green) !important;
        border: 1px solid var(--gp-border);
        border-radius: 12px !important;
        box-shadow: none !important;
    }

    .login-container h2,
    .register-container h2,
    .shell h1,
    body > main.card h1 {
        color: var(--gp-green) !important;
        font-size: 24px !important;
        font-weight: 800 !important;
        letter-spacing: 0 !important;
    }

    .login-container h2::after,
    .register-container h2::after {
        color: var(--gp-muted) !important;
        font-size: 13px !important;
    }

    .login-container button,
    .register-container button,
    body > main.card button,
    .btn-login {
        border-radius: 7px !important;
        background: var(--gp-green) !important;
        box-shadow: none !important;
        font-size: 13px !important;
        min-height: 40px !important;
    }

    .app .main {
        min-width: 0;
        padding: 23px 28px 28px !important;
    }

    .app:has(.nasabah-sidebar) .main {
        min-height: 100vh;
    }

    .app:has(.nasabah-sidebar) .grid,
    .app:has(.nasabah-sidebar) .profile-content,
    .app:has(.nasabah-sidebar) .form-content {
        max-width: none !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .page-header,
    .nasabah-page-header {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 20px !important;
        width: 100%;
        margin: 0 0 18px !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .page-intro,
    .header-content {
        min-width: 0;
        max-width: none !important;
        margin: 0 !important;
    }

    .header-content h1,
    .header-content h2 {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0 0 6px !important;
        color: var(--gp-green) !important;
        font-size: 25px !important;
        font-weight: 800 !important;
        line-height: 1.05;
        letter-spacing: 0 !important;
    }

    .header-content h1 svg,
    .header-content h2 svg {
        width: 22px !important;
        height: 22px !important;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
        flex: 0 0 auto;
    }

    .header-content p,
    .header-content .subtle,
    .subtle {
        margin: 0 !important;
        color: var(--gp-muted) !important;
        font-size: 13px !important;
        line-height: 1.45;
    }

    .nasabah-header-side {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        max-width: 620px;
    }

    .nasabah-status {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
        color: #5e6c63;
        font-size: 12px;
    }

    .nasabah-status span {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 6px 10px;
        border: 1px solid #e2e8e3;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.72);
        white-space: nowrap;
    }

    .nasabah-status span:first-child {
        background: #edf5ef;
        border-color: #d7e8dc;
        color: var(--gp-green);
        font-weight: 800;
    }

    .card > .nasabah-page-header {
        margin-bottom: 20px !important;
    }

    .btn-outline,
    .btn-transaksi,
    .back-btn,
    .btn-export {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 7px 11px !important;
        border: 1px solid #d8e1da !important;
        border-radius: 6px !important;
        background: #f4f6f4 !important;
        color: #33443a !important;
        box-shadow: none !important;
        transform: none !important;
        text-decoration: none !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    @media (max-width: 768px) {
        .card-body,
        .gp-card-body {
            padding: 18px !important;
        }

        .app .main {
            padding: 18px !important;
        }

        .page-header,
        .gp-card-header,
        .section-header,
        .table-header {
            flex-direction: column;
        }

        .nasabah-header-side,
        .nasabah-status {
            justify-content: flex-start;
            max-width: 100%;
        }

        .nasabah-status span {
            white-space: normal;
        }

        .btn,
        .btn-outline,
        .btn-transaksi,
        .back-btn,
        .btn-export {
            width: auto;
        }

        .gp-toast-stack {
            top: 12px;
            right: 12px;
            left: 12px;
            width: auto;
        }

        .gp-date-range {
            align-items: stretch;
            gap: 10px;
            flex-wrap: wrap;
            width: 100%;
        }

        .gp-date-field {
            flex-basis: 100%;
            min-width: 0;
        }

        .gp-date-separator {
            padding-left: 2px;
        }

        .filter-group.gp-date-filter {
            flex-basis: 100%;
            min-width: 0;
            max-width: none;
        }
    }
</style>
<script>
    document.addEventListener('click', function (event) {
        const dismissButton = event.target.closest('[data-dismiss-target]');

        if (!dismissButton) {
            return;
        }

        const targetSelector = dismissButton.getAttribute('data-dismiss-target');
        const target = targetSelector ? document.querySelector(targetSelector) : null;

        if (target) {
            target.remove();
        }
    });

    (function () {
        const iconPaths = {
            success: '<path d="M5 11.917 9.724 16.5 19 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
            danger: '<path d="M6 18 17.94 6M18 18 6.06 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
            warning: '<path d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
        };

        function ensureToastStack() {
            let stack = document.querySelector('.gp-toast-stack');

            if (!stack) {
                stack = document.createElement('div');
                stack.className = 'gp-toast-stack';
                document.body.appendChild(stack);
            }

            return stack;
        }

        window.gpShowToast = function (message, type = 'warning') {
            const stack = ensureToastStack();
            const toastId = `gp-js-toast-${Date.now()}-${Math.random().toString(36).slice(2)}`;
            const toast = document.createElement('div');
            const safeType = ['success', 'danger', 'warning'].includes(type) ? type : 'warning';

            toast.id = toastId;
            toast.className = `gp-toast gp-toast-${safeType}`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="gp-toast-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">${iconPaths[safeType]}</svg>
                    <span class="visually-hidden">${safeType} icon</span>
                </div>
                <div class="gp-toast-message"></div>
                <button type="button" class="gp-toast-close" data-dismiss-target="#${toastId}" aria-label="Close">
                    <span class="visually-hidden">Close</span>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 18 17.94 6M18 18 6.06 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </button>
            `;

            toast.querySelector('.gp-toast-message').textContent = String(message || '');
            stack.appendChild(toast);

            window.setTimeout(function () {
                toast.remove();
            }, 4200);

            return toast;
        };

        window.alert = function (message) {
            window.gpShowToast(message, 'warning');
        };
    })();
</script>
