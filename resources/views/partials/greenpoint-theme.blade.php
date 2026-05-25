<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

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
        font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif !important;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    p,
    li,
    td,
    .gp-subtitle,
    .section-subtitle,
    .card-text,
    .report-card-subtitle,
    .text-muted,
    .subtle,
    .header-content p,
    .page-intro p {
        font-weight: 400 !important;
    }

    strong,
    b {
        font-weight: 600;
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
        font-weight: 700 !important;
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
        font-weight: 600;
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
        font-weight: 600;
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
        font-weight: 700;
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
        font-weight: 700 !important;
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
        font-weight: 600;
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
        font-weight: 400;
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

    .gp-date-field.is-datepicker-open .date-input {
        border-color: var(--gp-green) !important;
        box-shadow: 0 0 0 3px rgba(47, 95, 62, 0.12) !important;
    }

    .gp-datepicker-panel {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        z-index: 1250;
        display: none;
        width: 292px;
        padding: 12px;
        background: #ffffff;
        border: 1px solid var(--gp-border);
        border-radius: 10px;
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.14);
    }

    .gp-date-field.is-datepicker-open .gp-datepicker-panel {
        display: block;
    }

    .gp-datepicker-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
    }

    .gp-datepicker-title {
        color: var(--gp-text);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.2;
    }

    .gp-datepicker-nav {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        color: #33443a;
        background: #f4f6f4;
        border: 1px solid #d8e1da;
        border-radius: 7px;
        cursor: pointer;
        transition: background-color 0.16s ease, color 0.16s ease, border-color 0.16s ease;
    }

    .gp-datepicker-nav:hover {
        color: #ffffff;
        background: var(--gp-green);
        border-color: var(--gp-green);
    }

    .gp-datepicker-nav svg {
        width: 16px;
        height: 16px;
    }

    .gp-datepicker-weekdays,
    .gp-datepicker-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .gp-datepicker-weekday {
        padding: 6px 0;
        color: var(--gp-muted);
        font-size: 11px;
        font-weight: 600;
        line-height: 1;
        text-align: center;
    }

    .gp-datepicker-day {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-width: 0;
        height: 34px;
        padding: 0;
        color: var(--gp-text);
        background: transparent;
        border: 1px solid transparent;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        cursor: pointer;
        transition: background-color 0.16s ease, color 0.16s ease, border-color 0.16s ease;
    }

    .gp-datepicker-day:hover:not(:disabled) {
        color: var(--gp-green);
        background: #edf5ef;
        border-color: #d7e8dc;
    }

    .gp-datepicker-day.is-today:not(.is-selected) {
        border-color: #bdd8c5;
        color: var(--gp-green);
    }

    .gp-datepicker-day.is-selected {
        color: #ffffff;
        background: var(--gp-green);
        border-color: var(--gp-green);
    }

    .gp-datepicker-day.is-muted {
        color: #a5aea8;
    }

    .gp-datepicker-day:disabled {
        color: #c8d0cb;
        cursor: not-allowed;
        background: transparent;
    }

    @media (max-width: 480px) {
        .gp-datepicker-panel {
            right: 0;
            width: min(292px, calc(100vw - 48px));
        }
    }

    .gp-date-separator {
        color: var(--gp-muted);
        font-size: 13px;
        font-weight: 500;
        line-height: 1;
        flex: 0 0 auto;
    }

    .filter-group.gp-date-filter {
        flex: 1 1 620px;
        min-width: min(100%, 620px);
        max-width: 620px;
    }

    .gp-custom-select {
        position: relative;
        width: 100%;
        min-width: 0;
    }

    .gp-native-select {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        min-height: 1px !important;
        padding: 0 !important;
        margin: 0 !important;
        opacity: 0 !important;
        pointer-events: none !important;
        clip: rect(0, 0, 0, 0) !important;
        clip-path: inset(50%) !important;
    }

    .gp-custom-select-trigger {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
        width: 100% !important;
        min-height: 38px !important;
        padding: 8px 11px !important;
        color: var(--gp-text) !important;
        background: #ffffff !important;
        border: 1px solid #d8e1da !important;
        border-radius: 7px !important;
        box-shadow: none !important;
        font-size: 13px !important;
        font-weight: 400 !important;
        line-height: 1.25 !important;
        text-align: left !important;
        cursor: pointer !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .gp-custom-select-trigger:hover,
    .gp-custom-select.is-open .gp-custom-select-trigger {
        border-color: var(--gp-green) !important;
        background: #fbfdfb !important;
    }

    .gp-custom-select-trigger:focus {
        border-color: var(--gp-green) !important;
        box-shadow: 0 0 0 3px rgba(47, 95, 62, 0.12) !important;
        outline: none !important;
    }

    .gp-custom-select.is-disabled .gp-custom-select-trigger {
        cursor: not-allowed !important;
        opacity: 0.65;
    }

    .gp-custom-select-value {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .gp-custom-select-chevron {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        color: #33443a;
        transition: transform 0.18s ease;
    }

    .gp-custom-select.is-open .gp-custom-select-chevron {
        transform: rotate(180deg);
    }

    .gp-custom-select-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 1200;
        display: none;
        max-height: 236px;
        padding: 5px;
        overflow: auto;
        background: #ffffff;
        border: 1px solid var(--gp-border);
        border-radius: 8px;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.14);
    }

    .gp-custom-select.is-open .gp-custom-select-menu {
        display: grid;
        gap: 2px;
    }

    .gp-custom-select-option {
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        min-height: 34px !important;
        padding: 8px 10px !important;
        color: var(--gp-text) !important;
        background: transparent !important;
        border: 0 !important;
        border-radius: 6px !important;
        box-shadow: none !important;
        font-size: 13px !important;
        font-weight: 400 !important;
        line-height: 1.25 !important;
        text-align: left !important;
        cursor: pointer !important;
        transition: background-color 0.16s ease, color 0.16s ease;
    }

    .gp-custom-select-option:hover,
    .gp-custom-select-option.is-active {
        background: #edf5ef !important;
        color: var(--gp-green) !important;
    }

    .gp-custom-select-option.is-selected {
        background: var(--gp-green) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    .gp-custom-select-option:disabled {
        cursor: not-allowed !important;
        opacity: 0.52;
    }

    .gp-custom-select-sm .gp-custom-select-trigger {
        min-height: 34px !important;
        padding: 6px 10px !important;
        font-size: 12px !important;
    }

    .gp-custom-select-sm .gp-custom-select-option {
        min-height: 30px !important;
        padding: 7px 9px !important;
        font-size: 12px !important;
    }

    .pagination-controls .gp-custom-select {
        width: 78px;
        flex: 0 0 78px;
    }

    .gp-filter-form .gp-custom-select,
    .dt-length .gp-custom-select {
        width: auto;
        min-width: 78px;
    }

    .select-wrapper .gp-custom-select + i[data-lucide="chevron-down"],
    .select-wrapper .gp-custom-select + svg[data-lucide="chevron-down"],
    .select-wrapper .gp-custom-select + .lucide-chevron-down {
        display: none !important;
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
        font-weight: 700;
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
        font-weight: 400;
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
        font-weight: 700 !important;
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
        font-weight: 700 !important;
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
        font-weight: 600;
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
        font-weight: 600 !important;
    }

    .btn svg,
    .btn-secondary svg,
    .btn-outline svg,
    .btn-transaksi svg,
    .back-btn svg,
    .btn-export svg,
    .filter-actions button svg,
    .filter-actions a svg,
    .table-actions button svg,
    .table-actions a svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        flex: 0 0 18px !important;
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
    (function () {
        const enhancedAttr = 'data-gp-select-enhanced';
        let activeSelect = null;

        function optionText(option) {
            return option ? (option.textContent || option.label || '').trim() : '';
        }

        function selectedOption(select) {
            return select.options[select.selectedIndex] || select.querySelector('option:not([disabled])') || select.options[0] || null;
        }

        function closeSelect(wrapper) {
            if (!wrapper) {
                return;
            }

            wrapper.classList.remove('is-open');
            wrapper.querySelector('.gp-custom-select-trigger')?.setAttribute('aria-expanded', 'false');

            if (activeSelect === wrapper) {
                activeSelect = null;
            }
        }

        function closeAllSelects(except = null) {
            document.querySelectorAll('.gp-custom-select.is-open').forEach(function (wrapper) {
                if (wrapper !== except) {
                    closeSelect(wrapper);
                }
            });
        }

        function enhanceSelect(select) {
            if (!(select instanceof HTMLSelectElement) || select.multiple || select.hasAttribute(enhancedAttr)) {
                return;
            }

            select.setAttribute(enhancedAttr, 'true');

            const parent = select.parentNode;
            const wrapper = document.createElement('div');
            const trigger = document.createElement('button');
            const value = document.createElement('span');
            const menu = document.createElement('div');
            const selectMinWidth = select.style.minWidth;
            const selectWidth = select.style.width;
            const selectMaxWidth = select.style.maxWidth;
            const uid = select.id || `gp-select-${Math.random().toString(36).slice(2)}`;

            wrapper.className = 'gp-custom-select';

            if (select.classList.contains('form-select-sm')) {
                wrapper.classList.add('gp-custom-select-sm');
            }

            if (select.closest('.pagination-controls')) {
                wrapper.classList.add('gp-custom-select-sm');
            }

            if (selectWidth) {
                wrapper.style.width = selectWidth;
            }

            if (selectMinWidth) {
                wrapper.style.minWidth = selectMinWidth;
            }

            if (selectMaxWidth) {
                wrapper.style.maxWidth = selectMaxWidth;
            }

            parent.insertBefore(wrapper, select);
            wrapper.appendChild(select);

            select.classList.add('gp-native-select');
            select.tabIndex = -1;

            trigger.type = 'button';
            trigger.className = 'gp-custom-select-trigger';
            trigger.setAttribute('aria-haspopup', 'listbox');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.setAttribute('aria-controls', `${uid}-menu`);

            value.className = 'gp-custom-select-value';
            trigger.appendChild(value);
            trigger.insertAdjacentHTML('beforeend', `
                <svg class="gp-custom-select-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            `);

            menu.id = `${uid}-menu`;
            menu.className = 'gp-custom-select-menu';
            menu.setAttribute('role', 'listbox');
            wrapper.appendChild(trigger);
            wrapper.appendChild(menu);

            function buildMenu() {
                menu.innerHTML = '';

                Array.from(select.options).forEach(function (option, index) {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'gp-custom-select-option';
                    item.textContent = optionText(option);
                    item.dataset.index = String(index);
                    item.setAttribute('role', 'option');
                    item.setAttribute('aria-selected', option.selected ? 'true' : 'false');

                    if (option.disabled) {
                        item.disabled = true;
                        item.setAttribute('aria-disabled', 'true');
                    }

                    if (option.selected) {
                        item.classList.add('is-selected');
                    }

                    item.addEventListener('click', function () {
                        if (option.disabled) {
                            return;
                        }

                        select.selectedIndex = index;
                        select.dispatchEvent(new Event('input', { bubbles: true }));
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        closeSelect(wrapper);
                        trigger.focus();
                    });

                    menu.appendChild(item);
                });
            }

            function sync() {
                const current = selectedOption(select);

                value.textContent = optionText(current) || 'Pilih opsi';
                trigger.disabled = select.disabled;
                wrapper.classList.toggle('is-disabled', select.disabled);

                menu.querySelectorAll('.gp-custom-select-option').forEach(function (item) {
                    const isSelected = Number(item.dataset.index) === select.selectedIndex;
                    item.classList.toggle('is-selected', isSelected);
                    item.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                });
            }

            function openSelect() {
                if (select.disabled) {
                    return;
                }

                closeAllSelects(wrapper);
                buildMenu();
                sync();
                wrapper.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                activeSelect = wrapper;

                const selectedItem = menu.querySelector('.gp-custom-select-option.is-selected:not(:disabled)') || menu.querySelector('.gp-custom-select-option:not(:disabled)');
                selectedItem?.scrollIntoView({ block: 'nearest' });
            }

            function moveActive(direction) {
                const items = Array.from(menu.querySelectorAll('.gp-custom-select-option:not(:disabled)'));
                const active = menu.querySelector('.gp-custom-select-option.is-active') || menu.querySelector('.gp-custom-select-option.is-selected');
                let index = Math.max(0, items.indexOf(active));

                index = Math.min(items.length - 1, Math.max(0, index + direction));
                items.forEach(function (item) {
                    item.classList.remove('is-active');
                });
                items[index]?.classList.add('is-active');
                items[index]?.scrollIntoView({ block: 'nearest' });
            }

            trigger.addEventListener('click', function () {
                if (wrapper.classList.contains('is-open')) {
                    closeSelect(wrapper);
                    return;
                }

                openSelect();
            });

            trigger.addEventListener('keydown', function (event) {
                if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
                    event.preventDefault();

                    if (!wrapper.classList.contains('is-open')) {
                        openSelect();
                    }

                    if (event.key === 'ArrowDown') {
                        moveActive(1);
                    }

                    if (event.key === 'ArrowUp') {
                        moveActive(-1);
                    }

                    if (event.key === 'Enter' || event.key === ' ') {
                        (menu.querySelector('.gp-custom-select-option.is-active:not(:disabled)') || menu.querySelector('.gp-custom-select-option.is-selected:not(:disabled)'))?.click();
                    }
                }

                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeSelect(wrapper);
                }
            });

            select.addEventListener('change', sync);
            select.addEventListener('input', sync);
            select.addEventListener('focus', function () {
                trigger.focus();
            });
            select.form?.addEventListener('reset', function () {
                window.setTimeout(sync, 0);
            });

            new MutationObserver(function () {
                buildMenu();
                sync();
            }).observe(select, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['selected', 'disabled', 'label']
            });

            select.gpSyncCustomSelect = sync;
            buildMenu();
            sync();
        }

        function initCustomSelects(root = document) {
            if (root instanceof HTMLSelectElement) {
                enhanceSelect(root);
                return;
            }

            root.querySelectorAll?.('select').forEach(enhanceSelect);
        }

        document.addEventListener('DOMContentLoaded', function () {
            initCustomSelects();

            document.addEventListener('click', function (event) {
                if (!event.target.closest('.gp-custom-select')) {
                    closeAllSelects();
                }
            });

            new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            initCustomSelects(node);
                        }
                    });
                });
            }).observe(document.body, { childList: true, subtree: true });
        });

        window.gpRefreshCustomSelects = initCustomSelects;
        window.gpCloseCustomSelects = closeAllSelects;
    })();

    (function () {
        const enhancedAttr = 'data-gp-datepicker-enhanced';
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        let activeField = null;

        function pad(value) {
            return String(value).padStart(2, '0');
        }

        function toIso(date) {
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
        }

        function parseDate(value) {
            const text = String(value || '').trim();
            let match = text.match(/^(\d{4})-(\d{2})-(\d{2})$/);

            if (match) {
                return new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
            }

            match = text.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);

            if (match) {
                return new Date(Number(match[3]), Number(match[2]) - 1, Number(match[1]));
            }

            return null;
        }

        function isValidDate(date) {
            return date instanceof Date && !Number.isNaN(date.getTime());
        }

        function sameDay(first, second) {
            return isValidDate(first) && isValidDate(second)
                && first.getFullYear() === second.getFullYear()
                && first.getMonth() === second.getMonth()
                && first.getDate() === second.getDate();
        }

        function closeDatepicker(field) {
            if (!field) {
                return;
            }

            field.classList.remove('is-datepicker-open');

            if (activeField === field) {
                activeField = null;
            }
        }

        function closeAllDatepickers(except = null) {
            document.querySelectorAll('.gp-date-field.is-datepicker-open').forEach(function (field) {
                if (field !== except) {
                    closeDatepicker(field);
                }
            });
        }

        function enhanceDateInput(input) {
            if (!(input instanceof HTMLInputElement) || input.hasAttribute(enhancedAttr)) {
                return;
            }

            const field = input.closest('.gp-date-field');

            if (!field) {
                return;
            }

            input.setAttribute(enhancedAttr, 'true');

            if (input.type === 'date') {
                input.type = 'text';
            }

            input.autocomplete = 'off';
            input.inputMode = 'numeric';

            const panel = document.createElement('div');
            panel.className = 'gp-datepicker-panel';
            field.appendChild(panel);

            let viewDate = parseDate(input.value) || new Date();

            function minDate() {
                return parseDate(input.min);
            }

            function maxDate() {
                return parseDate(input.max);
            }

            function render() {
                const selected = parseDate(input.value);
                const min = minDate();
                const max = maxDate();
                const today = new Date();
                const firstOfMonth = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
                const start = new Date(firstOfMonth);
                start.setDate(firstOfMonth.getDate() - firstOfMonth.getDay());

                panel.innerHTML = `
                    <div class="gp-datepicker-header">
                        <button type="button" class="gp-datepicker-nav" data-datepicker-nav="prev" aria-label="Bulan sebelumnya">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                        </button>
                        <div class="gp-datepicker-title">${monthNames[viewDate.getMonth()]} ${viewDate.getFullYear()}</div>
                        <button type="button" class="gp-datepicker-nav" data-datepicker-nav="next" aria-label="Bulan berikutnya">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                        </button>
                    </div>
                    <div class="gp-datepicker-weekdays">
                        ${dayNames.map(function (day) { return `<div class="gp-datepicker-weekday">${day}</div>`; }).join('')}
                    </div>
                    <div class="gp-datepicker-grid"></div>
                `;

                const grid = panel.querySelector('.gp-datepicker-grid');

                for (let index = 0; index < 42; index += 1) {
                    const day = new Date(start);
                    day.setDate(start.getDate() + index);

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'gp-datepicker-day';
                    button.textContent = String(day.getDate());
                    button.dataset.date = toIso(day);

                    if (day.getMonth() !== viewDate.getMonth()) {
                        button.classList.add('is-muted');
                    }

                    if (sameDay(day, today)) {
                        button.classList.add('is-today');
                    }

                    if (sameDay(day, selected)) {
                        button.classList.add('is-selected');
                    }

                    if ((isValidDate(min) && day < min) || (isValidDate(max) && day > max)) {
                        button.disabled = true;
                    }

                    grid.appendChild(button);
                }
            }

            function openDatepicker() {
                closeAllDatepickers(field);
                viewDate = parseDate(input.value) || viewDate || new Date();
                render();
                field.classList.add('is-datepicker-open');
                activeField = field;
            }

            function normalizeInputValue() {
                const parsed = parseDate(input.value);

                if (isValidDate(parsed)) {
                    input.value = toIso(parsed);
                }
            }

            input.addEventListener('focus', openDatepicker);
            input.addEventListener('click', openDatepicker);

            input.addEventListener('blur', function () {
                normalizeInputValue();
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            input.form?.addEventListener('submit', normalizeInputValue);

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeDatepicker(field);
                }
            });

            panel.addEventListener('mousedown', function (event) {
                event.preventDefault();
            });

            panel.addEventListener('click', function (event) {
                const nav = event.target.closest('[data-datepicker-nav]');
                const day = event.target.closest('.gp-datepicker-day');

                if (nav) {
                    viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() + (nav.dataset.datepickerNav === 'next' ? 1 : -1), 1);
                    render();
                    return;
                }

                if (day && !day.disabled) {
                    input.value = day.dataset.date;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    closeDatepicker(field);
                    input.focus();
                }
            });

            new MutationObserver(function () {
                if (field.classList.contains('is-datepicker-open')) {
                    render();
                }
            }).observe(input, {
                attributes: true,
                attributeFilter: ['min', 'max', 'value']
            });
        }

        function initDatepickers(root = document) {
            const selector = '.gp-date-range .date-input, .gp-date-range [data-gp-datepicker]';

            if (root instanceof HTMLInputElement && root.matches(selector)) {
                enhanceDateInput(root);
                return;
            }

            root.querySelectorAll?.(selector).forEach(enhanceDateInput);
        }

        document.addEventListener('DOMContentLoaded', function () {
            initDatepickers();

            document.addEventListener('mousedown', function (event) {
                if (!event.target.closest('.gp-date-field')) {
                    closeAllDatepickers();
                }
            });

            new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            initDatepickers(node);
                        }
                    });
                });
            }).observe(document.body, { childList: true, subtree: true });
        });

        window.gpRefreshDatepickers = initDatepickers;
        window.gpCloseDatepickers = closeAllDatepickers;
    })();

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
