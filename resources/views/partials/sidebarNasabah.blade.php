<style>
.sidebar {
    background: linear-gradient(135deg, #000000, #059669);
    color: white;
    width: 280px;
    padding: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    box-shadow: 2px 0 4px rgba(0,0,0,0.1);
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.brand img {
    width: 40px;
    height: 40px;
}

.brand h1 {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}

.nav {
    flex: 1;
    overflow-y: auto;
    padding: 12px 0;
}

.nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    transition: all 0.2s;
    font-size: 14px;
    border-left: 3px solid transparent;
}

.nav a:hover {
    background: rgba(255,255,255,0.05);
    color: white;
}

.nav a.active {
    background: rgba(16, 185, 129, 0.1);
    border-left-color: #10b981;
    color: #ffffff;
}

.icon {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    cursor: pointer;
    border: none;
    background: none;
    font-size: 14px;
    border-top: 1px solid rgba(255,255,255,0.1);
    width: 100%;
    transition: all 0.2s;
}

.logout-btn:hover {
    background: rgba(255,255,255,0.05);
    color: white;
}

/* Scrollbar styling */
.nav::-webkit-scrollbar {
    width: 6px;
}

.nav::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
}

.nav::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}

.nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.3);
}

.logo-white {
    width: 40px;
    height: 40px;
    object-fit: contain;
    filter: brightness(0) invert(1);
}
</style>

@php
    $activePage = $activePage ?? '';
@endphp

<div class="sidebar">
    <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo" class="logo-white">
        <h1>GreenPoint</h1>
    </div>

    <div class="nav">
        <a href="{{ route('nasabah.dashboard') }}" class="{{ ($activePage == 'dashboard') ? 'active' : '' }}">
            <svg class="icon" viewBox="0 0 24 24">
                <rect x="3" y="3" width="8" height="8"/>
                <rect x="13" y="3" width="8" height="8"/>
                <rect x="3" y="13" width="8" height="8"/>
                <rect x="13" y="13" width="8" height="8"/>
            </svg>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('nasabah.transaksi') }}" class="{{ ($activePage == 'transaksi') ? 'active' : '' }}">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M9 11H7v2h2V11zm4 0h-2v2h2V11zm4 0h-2v2h2V11zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
            </svg>
            <span>Riwayat PPOB</span>
        </a>

        <a href="{{ route('nasabah.riwayat-setor') }}" class="{{ ($activePage == 'riwayat-setor') ? 'active' : '' }}">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
            <span>Riwayat Setor</span>
        </a>

        <a href="{{ route('nasabah.profil') }}" class="{{ ($activePage == 'profil') ? 'active' : '' }}">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            <span>Profil Saya</span>
        </a>
    </div>

    <a href="javascript:void(0);" class="logout-btn" onclick="confirmLogout()">
        <svg class="icon" viewBox="0 0 24 24">
            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
        </svg>
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
