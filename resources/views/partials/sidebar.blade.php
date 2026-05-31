<style>
  .sidebar {
    width: 222px;
    min-width: 222px;
    height: 100vh;
    position: sticky;
    top: 0;
    flex: 0 0 222px;
    background: #ffffff;
    color: #2f5f3e;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #e7ece8;
    box-shadow: 8px 0 24px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    z-index: 5;
  }

  .sidebar .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 24px 18px 22px;
    min-height: 78px;
  }

  .sidebar .brand img {
    width: 34px;
    height: 34px;
    object-fit: contain;
    flex-shrink: 0;
  }

  .sidebar .brand h1 {
    margin: 0;
    color: #2f5f3e;
    font-size: 19px;
    font-weight: 800;
    line-height: 1;
  }

  .sidebar .nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 0 12px 18px;
    overflow-y: auto;
  }

  .sidebar .nav a,
  .sidebar .sidebar-logout {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 42px;
    padding: 10px 12px;
    border-radius: 7px;
    color: #2f5f3e;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.2;
    text-decoration: none;
    transition: background-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
  }

  .sidebar .nav a:hover,
  .sidebar .sidebar-logout:hover {
    background: #eef6f0;
    color: #254d33;
    transform: translateX(2px);
  }

  .sidebar .nav a.active {
    background: #2f5f3e;
    color: #ffffff;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
  }

  .sidebar .icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    object-fit: contain;
  }

  .sidebar .nav a.active .icon {
    filter: brightness(0) invert(1);
  }

  .sidebar .chevron {
    width: 15px;
    height: 15px;
    margin-left: auto;
    stroke: currentColor;
    fill: none;
    stroke-width: 2.4;
    transition: transform 0.2s ease;
  }

  .sidebar .nav-submenu {
    display: none;
    margin: 2px 0 4px 30px;
    padding: 4px 0;
    border-left: 1px solid #dbe8df;
  }

  .sidebar .nav-submenu a {
    min-height: 32px;
    padding: 8px 10px 8px 14px;
    border-radius: 6px;
    color: #54715d;
    font-size: 12px;
    font-weight: 600;
  }

  .sidebar .nav-submenu a:hover {
    background: #f4f8f5;
    color: #2f5f3e;
    transform: none;
  }

  .sidebar .sidebar-footer {
    border-top: 1px solid #e8eee9;
    background: #ffffff;
  }

  .sidebar .sidebar-logout {
    margin: 12px 12px 8px;
  }

  .sidebar .sidebar-logout .icon {
    filter: none;
  }

  .sidebar .user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px 16px;
    border-top: 1px solid #eef2ef;
  }

  .sidebar .avatar {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    background: #2f5f3e;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
  }

  .sidebar .user-info {
    min-width: 0;
    display: grid;
    gap: 2px;
  }

  .sidebar .user-info .role {
    color: #2f5f3e;
    font-size: 13px;
    font-weight: 800;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .sidebar .user-info .name {
    color: #111827;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .sidebar .nav::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar .nav::-webkit-scrollbar-track {
    background: transparent;
  }

  .sidebar .nav::-webkit-scrollbar-thumb {
    background: #d9e5dc;
    border-radius: 999px;
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 100%;
      min-width: 100%;
      height: auto;
      position: relative;
      top: auto;
      flex-basis: auto;
      max-height: none;
      overflow: visible;
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .sidebar .brand {
      min-height: 58px;
      padding: 14px 16px;
    }

    .sidebar .nav {
      flex-direction: row;
      overflow-x: auto;
      overflow-y: hidden;
      padding: 0 12px 12px;
    }

    .sidebar .nav a {
      white-space: nowrap;
      min-width: max-content;
    }

    .sidebar .nav-dropdown {
      min-width: max-content;
    }

    .sidebar .nav-submenu {
      position: absolute;
      margin: 4px 0 0;
      padding: 8px;
      border: 1px solid #dbe8df;
      border-radius: 8px;
      background: #ffffff;
      box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
    }

    .sidebar .sidebar-footer {
      display: block;
      padding: 0 12px 12px;
      border-top: 0;
    }

    .sidebar .sidebar-logout {
      justify-content: center;
      width: 100%;
      min-height: 40px;
      margin: 0;
      background: #f4f8f5;
      border: 1px solid #d8e1da;
    }

    .sidebar .user-profile {
      display: none;
    }
  }
</style>

@php
  $adminRole = session('admin_logged_in') ? ucfirst(session('admin_role') ?? 'Admin') : 'Admin';
  $adminName = session('admin_logged_in') ? explode('@', session('admin_email') ?? 'admin@greenpoint.local')[0] : 'Admin';
  $adminInitial = session('admin_logged_in') ? strtoupper(substr(session('admin_role') ?? 'AD', 0, 2)) : 'AD';
@endphp

<aside class="sidebar">
  <div class="brand">
    <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo">
    <h1>GreenPoint</h1>
  </div>

  <nav class="nav">
    <a href="{{ url('/admin/dashboard') }}" class="{{ ($activePage == 'dashboard') ? 'active' : '' }}">
      <img src="{{ asset('images/Dashboard Layout.png') }}" alt="" class="icon" aria-hidden="true">
      <span>Dashboard</span>
    </a>

    <a href="{{ url('/admin/nasabah') }}" class="{{ ($activePage == 'nasabah') ? 'active' : '' }}">
      <img src="{{ asset('images/People.png') }}" alt="" class="icon" aria-hidden="true">
      <span>Daftar Nasabah</span>
    </a>

    <div class="nav-dropdown">
      <a href="javascript:void(0);" class="{{ ($activePage == 'transaksi') ? 'active' : '' }}" id="transaksiToggle">
        <img src="{{ asset('images/Money Circulation.png') }}" alt="" class="icon" aria-hidden="true">
        <span>Transaksi</span>
        <svg class="chevron" viewBox="0 0 24 24" aria-hidden="true">
          <path d="m6 9 6 6 6-6" />
        </svg>
      </a>
      <div class="nav-submenu" id="transaksiSubmenu" style="{{ ($activePage == 'transaksi') ? 'display: block;' : '' }}">
        <a href="{{ url('/admin/transaksi?tab=setor') }}">Permintaan Setor</a>
        <a href="{{ url('/admin/transaksi?tab=penarikan') }}">Permintaan Tarik</a>
        <a href="{{ url('/admin/transaksi?tab=history') }}">Riwayat</a>
      </div>
    </div>

    <a href="{{ url('/admin/sampah') }}" class="{{ ($activePage == 'sampah') ? 'active' : '' }}">
      <img src="{{ asset('images/Trash.png') }}" alt="" class="icon" aria-hidden="true">
      <span>Daftar Sampah</span>
    </a>

    <a href="{{ url('/admin/laporan') }}" class="{{ ($activePage == 'laporan') ? 'active' : '' }}">
      <img src="{{ asset('images/Activity History.png') }}" alt="" class="icon" aria-hidden="true">
      <span>Laporan</span>
    </a>

    @if (session('admin_logged_in') && session('admin_role') === 'superadmin')
      <a href="{{ url('/admin/pengaturan') }}" class="{{ ($activePage == 'pengaturan') ? 'active' : '' }}">
        <img src="{{ asset('images/Settings.png') }}" alt="" class="icon" aria-hidden="true">
        <span>Pengaturan Admin</span>
      </a>
    @endif
  </nav>

  <div class="sidebar-footer">
    <a
      href="{{ route('admin.logout') }}"
      class="sidebar-logout"
      data-gp-logout
      data-logout-variant="admin"
      data-logout-eyebrow="Sesi Admin"
      data-logout-title="Keluar dari panel admin?"
      data-logout-message="Sesi admin akan ditutup dan akses pengelolaan GreenPoint dihentikan dari perangkat ini."
      data-logout-note="Pastikan perubahan data nasabah, sampah, atau transaksi sudah tersimpan sebelum keluar."
      data-logout-confirm="Keluar dari Admin"
      data-logout-cancel="Tetap bekerja"
    >
      <img src="{{ asset('images/Logout.png') }}" alt="" class="icon" aria-hidden="true">
      <span>Logout</span>
    </a>

    <div class="user-profile">
      <div class="avatar">{{ $adminInitial }}</div>
      <div class="user-info">
        <span class="role">{{ $adminRole }}</span>
        <span class="name">{{ $adminName }}</span>
      </div>
    </div>
  </div>
</aside>

<script>
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
