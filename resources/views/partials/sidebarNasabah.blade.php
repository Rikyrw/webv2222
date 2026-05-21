<style>
:root {
    --nasabah-sidebar-width: 222px;
    --nasabah-sidebar-green: #2f5f3e;
    --nasabah-sidebar-green-soft: #edf3ee;
    --nasabah-sidebar-text: #244232;
    --nasabah-sidebar-muted: #6b7a70;
}

.nasabah-sidebar {
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
    box-shadow: 8px 0 24px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    z-index: 5;
}

.nasabah-sidebar .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 78px;
    padding: 24px 18px 22px;
}

.nasabah-sidebar .brand img {
    width: 34px;
    height: 34px;
    object-fit: contain;
    flex-shrink: 0;
}

.nasabah-sidebar .brand h1 {
    font-size: 19px;
    font-weight: 800;
    line-height: 1;
    margin: 0;
    color: var(--nasabah-sidebar-green);
}

.nasabah-sidebar .nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
    overflow-y: auto;
    padding: 0 12px 18px;
}

.nasabah-sidebar .nav a,
.nasabah-sidebar .logout-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 42px;
    padding: 10px 12px;
    color: var(--nasabah-sidebar-green);
    text-decoration: none;
    transition: all 0.2s;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.2;
    border-radius: 7px;
    border: 0;
    background: transparent;
}

.nasabah-sidebar .nav a:hover,
.nasabah-sidebar .logout-btn:hover {
    background: var(--nasabah-sidebar-green-soft);
    color: #254d33;
    transform: translateX(2px);
}

.nasabah-sidebar .nav a.active {
    background: var(--nasabah-sidebar-green);
    color: #ffffff;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
}

.nasabah-sidebar .icon {
    width: 18px;
    height: 18px;
    object-fit: contain;
    flex-shrink: 0;
}

.nasabah-sidebar .sidebar-footer {
    border-top: 1px solid #e8eee9;
    background: #ffffff;
}

.nasabah-sidebar .logout-btn {
    margin: 12px 12px 8px;
    width: calc(100% - 24px);
    cursor: pointer;
}

.nasabah-sidebar .user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px 16px;
    border-top: 1px solid #eef2ef;
}

.nasabah-sidebar .avatar {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    background: var(--nasabah-sidebar-green);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}

.nasabah-sidebar .user-info {
    min-width: 0;
    display: grid;
    gap: 2px;
}

.nasabah-sidebar .user-info .role {
    color: var(--nasabah-sidebar-green);
    font-size: 13px;
    font-weight: 800;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.nasabah-sidebar .user-info .name {
    color: #111827;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Scrollbar styling */
.nasabah-sidebar .nav::-webkit-scrollbar {
    width: 6px;
}

.nasabah-sidebar .nav::-webkit-scrollbar-track {
    background: transparent;
}

.nasabah-sidebar .nav::-webkit-scrollbar-thumb {
    background: #d7dfd9;
    border-radius: 3px;
}

.nasabah-sidebar .nav::-webkit-scrollbar-thumb:hover {
    background: #c4cec6;
}

.nasabah-sidebar .brand-logo {
    width: 34px;
    height: 34px;
    object-fit: contain;
}

.nasabah-sidebar .nav a.active .icon {
    filter: brightness(0) invert(1);
}

.main {
    margin-left: var(--nasabah-sidebar-width) !important;
}

@media (max-width: 768px) {
    .app:has(.nasabah-sidebar) {
        flex-direction: column;
    }

    .nasabah-sidebar {
        width: 100%;
        height: auto;
        position: relative;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .nasabah-sidebar .brand {
        min-height: 58px;
        padding: 14px 16px;
    }

    .nasabah-sidebar .nav {
        flex-direction: row;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 0 12px 12px;
    }

    .nasabah-sidebar .nav a {
        white-space: nowrap;
    }

    .nasabah-sidebar .sidebar-footer {
        display: none;
    }

    .main {
        margin-left: 0 !important;
    }
}
</style>

@php
    $activePage = $activePage ?? '';
    $nasabahDisplayName = session('nama_nasabah') ?? session('username') ?? 'Nasabah';
    $nasabahInitials = collect(explode(' ', trim($nasabahDisplayName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('') ?: 'NS';
@endphp

<div class="sidebar nasabah-sidebar">
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

    <div class="sidebar-footer">
        <a href="javascript:void(0);" class="logout-btn" onclick="confirmLogout()">
            <img src="{{ asset('images/Logout.png') }}" alt="" class="icon">
            <span>Logout</span>
        </a>

        <div class="user-profile" aria-label="Profil nasabah">
            <div class="avatar">{{ $nasabahInitials }}</div>
            <div class="user-info">
                <span class="role">Nasabah</span>
                <span class="name">{{ $nasabahDisplayName }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmLogout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '{{ route("nasabah.logout") }}';
        }
    }
</script>
