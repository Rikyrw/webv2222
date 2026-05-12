<style>
  .sidebar {
    background: linear-gradient(135deg, #000000, #059669);
    color: white;
    padding: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.2s;
    font-size: 14px;
    border-left: 3px solid transparent;
  }

  .nav a:hover {
    background: rgba(255, 255, 255, 0.05);
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

  .nav-submenu {
    background: rgba(255, 255, 255, 0.03);
    overflow: hidden;
    display: none;
  }

  .nav-submenu a {
    padding: 10px 16px 10px 48px;
    font-size: 13px;
    border-left: none;
  }

  /* User Footer Section */
  .sidebar-footer {
    padding: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }

  .user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
  }

  .user-profile:hover {
    background: rgba(255, 255, 255, 0.1);
  }

  .avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
  }

  .user-info {
    flex: 1;
    min-width: 0;
  }

  .user-info .role {
    display: block;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1;
    margin-bottom: 4px;
  }

  .user-info .name {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .logout-icon-btn {
    color: rgba(255, 255, 255, 0.5);
    transition: color 0.2s;
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }

  .logout-icon-btn:hover {
    color: #ef4444;
  }

  .logo-white {
    width: 40px;
    height: 40px;
    object-fit: contain;
    filter: brightness(0) invert(1);
  }

  /* Scrollbar styling */
  .nav::-webkit-scrollbar {
    width: 6px;
  }

  .nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
  }

  .nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
  }
</style>

<aside class="sidebar">
  <div class="brand">
    <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo" class="logo-white">
    <h1>GreenPoint</h1>
  </div>

  <nav class="nav">
    <a href="{{ url('/admin/dashboard') }}" class="{{ ($activePage == 'dashboard') ? 'active' : '' }}">
      <svg class="icon" viewBox="0 0 24 24">
        <rect x="3" y="3" width="8" height="8" />
        <rect x="13" y="3" width="8" height="8" />
        <rect x="3" y="13" width="8" height="8" />
        <rect x="13" y="13" width="8" height="8" />
      </svg>
      <span>Dashboard</span>
    </a>

    <a href="{{ url('/admin/nasabah') }}" class="{{ ($activePage == 'nasabah') ? 'active' : '' }}">
      <svg class="icon" viewBox="0 0 24 24">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
        <circle cx="9" cy="7" r="4" />
        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
      </svg>
      <span>Daftar Nasabah</span>
    </a>

    <div class="nav-dropdown">
      <a href="javascript:void(0);" class="{{ ($activePage == 'transaksi') ? 'active' : '' }}" id="transaksiToggle">
        <svg class="icon" viewBox="0 0 24 24">
          <path d="M16 3h5v5" />
          <path d="M8 21H3v-5" />
          <path d="M21 3 12 12" />
          <path d="m3 21 9-9" />
        </svg>
        <span style="flex: 1;">Transaksi</span>
        <svg class="icon chevron" style="width: 16px; height: 16px; transition: transform 0.2s;" viewBox="0 0 24 24">
          <path d="m6 9 6 6 6-6" />
        </svg>
      </a>
      <div class="nav-submenu" id="transaksiSubmenu"
        style="{{ ($activePage == 'transaksi') ? 'display: block;' : '' }}">
        <a href="{{ url('/admin/transaksi?tab=setor') }}">Permintaan Setor</a>
        <a href="{{ url('/admin/transaksi?tab=penarikan') }}">Permintaan Tarik</a>
        <a href="{{ url('/admin/transaksi?tab=history') }}">Riwayat</a>
      </div>
    </div>

    <a href="{{ url('/admin/sampah') }}" class="{{ ($activePage == 'sampah') ? 'active' : '' }}">
      <svg class="icon" viewBox="0 0 24 24">
        <path d="M3 6h18" />
        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
        <line x1="10" y1="11" x2="10" y2="17" />
        <line x1="14" y1="11" x2="14" y2="17" />
      </svg>
      <span>Daftar Sampah</span>
    </a>

    <a href="{{ url('/admin/laporan') }}" class="{{ ($activePage == 'laporan') ? 'active' : '' }}">
      <svg class="icon" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
        <polyline points="14 2 14 8 20 8" />
        <line x1="16" y1="13" x2="8" y2="13" />
        <line x1="16" y1="17" x2="8" y2="17" />
        <polyline points="10 9 9 9 8 9" />
      </svg>
      <span>Laporan</span>
    </a>

    @if (session('admin_logged_in') && session('admin_role') === 'superadmin')
      <a href="{{ url('/admin/pengaturan') }}" class="{{ ($activePage == 'pengaturan') ? 'active' : '' }}">
        <svg class="icon" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3" />
          <path
            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33 1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82 1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
        </svg>
        <span>Pengaturan</span>
      </a>
    @endif
  </nav>

  <div class="sidebar-footer">
    <div class="user-profile" onclick="confirmLogout()">
      <div class="avatar">
        {{ session('admin_logged_in') ? strtoupper(substr(session('admin_role') ?? 'AD', 0, 2)) : 'AD' }}
      </div>
      <div class="user-info">
        <span class="role">{{ session('admin_logged_in') ? ucfirst(session('admin_role')) : 'Admin' }}</span>
        <span class="name">{{ session('admin_logged_in') ? explode('@', session('admin_email'))[0] : 'Admin' }}</span>
      </div>
      <button class="logout-icon-btn" title="Logout">
        <svg class="icon" viewBox="0 0 24 24">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
          <polyline points="16 17 21 12 16 7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
      </button>
    </div>
  </div>
</aside>

<script>
  function confirmLogout() {
    if (confirm('Apakah Anda yakin ingin keluar?')) {
      window.location.href = '{{ url("/admin/logout") }}';
    }
  }

  // Transaksi dropdown toggle
  const transToggle = document.getElementById('transaksiToggle');
  const transSubmenu = document.getElementById('transaksiSubmenu');
  const transChevron = transToggle ? transToggle.querySelector('.chevron') : null;

  if (transToggle && transSubmenu) {
    transToggle.addEventListener('click', function (e) {
      e.preventDefault();
      const isHidden = window.getComputedStyle(transSubmenu).display === 'none';
      transSubmenu.style.display = isHidden ? 'block' : 'none';
      if (transChevron) {
        transChevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
      }
    });
  }
</script>