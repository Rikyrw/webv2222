<style>
:root {
    --nasabah-sidebar-width: 196px;
    --nasabah-sidebar-green: #24573a;
    --nasabah-sidebar-green-soft: #edf3ee;
    --nasabah-sidebar-text: #244232;
    --nasabah-sidebar-muted: #6b7a70;
}

.sidebar {
    background: #ffffff;
    color: var(--nasabah-sidebar-text);
    width: var(--nasabah-sidebar-width);
    padding: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    border-right: 1px solid #e6ebe7;
    box-shadow: none;
}

.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 26px 18px 22px;
}

.brand img {
    width: 24px;
    height: 24px;
}

.brand h1 {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    color: #234531;
}

.nav {
    flex: 1;
    overflow-y: auto;
    padding: 4px 12px;
}

.nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 38px;
    padding: 10px 12px;
    color: var(--nasabah-sidebar-text);
    text-decoration: none;
    transition: all 0.2s;
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    margin-bottom: 4px;
}

.nav a:hover {
    background: var(--nasabah-sidebar-green-soft);
    color: var(--nasabah-sidebar-green);
}

.nav a.active {
    background: var(--nasabah-sidebar-green);
    color: #ffffff;
}

.icon {
    width: 16px;
    height: 16px;
    object-fit: contain;
    flex-shrink: 0;
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 38px;
    margin: 0 12px 18px;
    padding: 10px 12px;
    color: var(--nasabah-sidebar-text);
    text-decoration: none;
    cursor: pointer;
    border: none;
    background: none;
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    width: calc(100% - 24px);
    transition: all 0.2s;
}

.logout-btn:hover {
    background: var(--nasabah-sidebar-green-soft);
    color: var(--nasabah-sidebar-green);
}

/* Scrollbar styling */
.nav::-webkit-scrollbar {
    width: 6px;
}

.nav::-webkit-scrollbar-track {
    background: transparent;
}

.nav::-webkit-scrollbar-thumb {
    background: #d7dfd9;
    border-radius: 3px;
}

.nav::-webkit-scrollbar-thumb:hover {
    background: #c4cec6;
}

.brand-logo {
    width: 24px;
    height: 24px;
    object-fit: contain;
}

.nav a.active .icon {
    filter: brightness(0) invert(1);
}

.main {
    margin-left: var(--nasabah-sidebar-width) !important;
}

@media (max-width: 768px) {
    .main {
        margin-left: 0 !important;
    }
}
</style>

@php
    $activePage = $activePage ?? '';
@endphp

<div class="sidebar">
    <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo" class="brand-logo">
        <h1>GreenPoint</h1>
    </div>

    <div class="nav">
        <a href="{{ route('nasabah.dashboard') }}" class="{{ ($activePage == 'dashboard') ? 'active' : '' }}">
            <img src="{{ asset('images/Dashboard Layout.png') }}" alt="" class="icon">
            <span>Dashboard</span>
        </a>

        <a href="{{ route('nasabah.setor') }}" class="{{ ($activePage == 'setor') ? 'active' : '' }}">
            <img src="{{ asset('images/Trash.png') }}" alt="" class="icon">
            <span>Setor Sampah</span>
        </a>
        
        <a href="{{ route('nasabah.transaksi') }}" class="{{ ($activePage == 'transaksi') ? 'active' : '' }}">
            <img src="{{ asset('images/Change.png') }}" alt="" class="icon">
            <span>Riwayat PPOB</span>
        </a>

        <a href="{{ route('nasabah.riwayat-setor') }}" class="{{ ($activePage == 'riwayat-setor') ? 'active' : '' }}">
            <img src="{{ asset('images/Activity History.png') }}" alt="" class="icon">
            <span>Riwayat Setor</span>
        </a>

        <a href="{{ route('nasabah.profil') }}" class="{{ ($activePage == 'profil') ? 'active' : '' }}">
            <img src="{{ asset('images/Person.png') }}" alt="" class="icon">
            <span>Profil Saya</span>
        </a>
    </div>

    <a href="javascript:void(0);" class="logout-btn" onclick="confirmLogout()">
        <img src="{{ asset('images/Logout.png') }}" alt="" class="icon">
        <span>Logout</span>
    </a>
</div>

<script>
    function confirmLogout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '{{ route("nasabah.logout") }}';
        }
    }
</script>
