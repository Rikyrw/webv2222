<aside class="sidebar" role="complementary" aria-label="Sidebar navigation">
<style>
.user {
  position: relative;
  margin-top: auto;
}

.avatar-dropdown {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}

.avatar-dropdown:hover {
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
  margin-right: 10px;
  flex-shrink: 0;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-info .role {
  display: block;
  font-size: 11px;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 2px;
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

.logout-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
  flex-shrink: 0;
}

.logout-btn:hover {
  color: white;
  background: rgba(255, 255, 255, 0.1);
}

/* Dropdown menu */
.user-dropdown {
  position: absolute;
  bottom: 100%;
  left: 0;
  right: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  margin-bottom: 10px;
  display: none;
  z-index: 1000;
  overflow: hidden;
}

.user:hover .user-dropdown {
  display: block;
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  color: #374151;
  text-decoration: none;
  transition: background 0.2s;
  border: none;
  width: 100%;
  text-align: left;
  background: none;
  cursor: pointer;
  font-size: 14px;
}

.dropdown-item:hover {
  background: #f3f4f6;
}

.dropdown-item i {
  margin-right: 10px;
  width: 16px;
  color: #6b7280;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 4px 0;
}

.logout-item {
  color: #dc2626;
}

.logout-item i {
  color: #dc2626;
}

.logout-item:hover {
  background: #fee2e2;
}

.nav-submenu a:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

/* Responsive */
@media (max-width: 768px) {
  .user-dropdown {
    position: fixed;
    bottom: 80px;
    left: 20px;
    right: 20px;
    width: auto;
  }
}

.logo-white {
    width: 40px;
    height: 40px;
    object-fit: contain;
    filter: brightness(0) invert(1);
}
</style>  

<!-- Brand -->
  <div class="brand">
    <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo" style="logo-white">
    <h1>GreenPoint</h1>
  </div>

  <!-- Navigation -->
  <nav class="nav" aria-label="Main menu">
    <a href="{{ url('/admin/dashboard') }}" class="{{ ($activePage == 'dashboard') ? 'active' : '' }}">
      <i class="i lucide-layout-dashboard"></i><span>Dashboard</span>
    </a>
    <a href="{{ url('/admin/nasabah') }}" class="{{ ($activePage == 'nasabah') ? 'active' : '' }}">
      <i class="i lucide-users"></i><span>Daftar Nasabah</span>
    </a>
    
    <!-- Transaksi with dropdown -->
    <div class="nav-dropdown" style="position: relative;">
      <a href="{{ url('/admin/transaksi') }}" class="{{ ($activePage == 'transaksi') ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
        <span style="display: flex; align-items: center; gap: 12px; flex: 1;">
          <i class="i lucide-repeat"></i><span>Transaksi</span>
        </span>
        <i class="i lucide-chevron-down" style="width: 16px; height: 16px; transform: rotate(0deg); transition: transform 0.2s;"></i>
      </a>
      <div class="nav-submenu" style="display: none; background: rgba(255,255,255,0.05); border-radius: 6px; margin-top: 4px; overflow: hidden;">
        <a href="{{ url('/admin/transaksi?tab=setor') }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; margin-left: 16px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px; border-radius: 6px; transition: all 0.2s;">
          <i class="lucide-arrow-down-to-line" style="width: 14px; height: 14px;"></i>Permintaan Setor Sampah
        </a>
        <a href="{{ url('/admin/transaksi?tab=penarikan') }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; margin-left: 16px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px; border-radius: 6px; transition: all 0.2s;">
          <i class="lucide-arrow-up-to-line" style="width: 14px; height: 14px;"></i>Permintaan Penarikan
        </a>
        <a href="{{ url('/admin/transaksi?tab=history') }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; margin-left: 16px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px; border-radius: 6px; transition: all 0.2s;">
          <i class="lucide-history" style="width: 14px; height: 14px;"></i>Riwayat Penarikan & Setor
        </a>
      </div>
    </div>

    <a href="{{ url('/admin/sampah') }}" class="{{ ($activePage == 'sampah') ? 'active' : '' }}">
      <i class="i lucide-trash-2"></i><span>Daftar Sampah</span>
    </a>
    <a href="{{ url('/admin/laporan') }}" class="{{ ($activePage == 'laporan') ? 'active' : '' }}">
      <i class="i lucide-file-chart-column"></i><span>Laporan</span>
    </a>
    
    @if (auth()->check() && auth()->user()->role === 'superadmin')
    <a href="{{ url('/admin/pengaturan') }}" class="{{ ($activePage == 'pengaturan') ? 'active' : '' }}">
      <i class="i lucide-settings"></i><span>Pengaturan Admin</span>
    </a>
    @endif
  </nav>

  <!-- User Footer -->
  <div class="user" role="contentinfo">
    <div class="avatar-dropdown">
      <div class="avatar">{{ auth()->check() ? strtoupper(substr(auth()->user()->role ?? 'AD', 0, 2)) : 'AD' }}</div>
      <div class="user-info">
        <span class="role">{{ auth()->check() ? ucfirst(auth()->user()->role) : 'Admin' }}</span>
        <span class="name">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</span>
      </div>
      <button class="logout-btn" onclick="logout()" title="Logout">
        <i class="lucide-log-out"></i>
      </button>
    </div>
    
    <!-- Dropdown menu (hidden by default) -->
    <div class="user-dropdown" id="userDropdown">
      <div class="dropdown-item">
        <i class="lucide-user"></i>
        <span>{{ auth()->check() ? auth()->user()->email : 'admin@greenpoint.com' }}</span>
      </div>
      <div class="dropdown-item">
        <i class="lucide-shield"></i>
        <span>Role: {{ auth()->check() ? ucfirst(auth()->user()->role) : 'Admin' }}</span>
      </div>
      <div class="dropdown-divider"></div>
      <a href="{{ url('/admin/logout') }}" class="dropdown-item logout-item" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
        <i class="lucide-log-out"></i>
        <span>Keluar</span>
      </a>
    </div>
  </div>
</aside>



<script>
function logout() {
  if (confirm('Apakah Anda yakin ingin keluar?')) {
    window.location.href = '{{ url("/admin/logout") }}';
  }
}

// Toggle dropdown on avatar click
document.querySelector('.avatar-dropdown').addEventListener('click', function(e) {
  e.stopPropagation();
  const dropdown = document.getElementById('userDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

// Close dropdown when clicking outside
document.addEventListener('click', function() {
  document.getElementById('userDropdown').style.display = 'none';
});

// Prevent dropdown from closing when clicking inside
document.getElementById('userDropdown').addEventListener('click', function(e) {
  e.stopPropagation();
});

// Transaksi dropdown toggle
const transDropdown = document.querySelector('.nav-dropdown');
if (transDropdown) {
  const transLink = transDropdown.querySelector('a:first-child');
  const transSubmenu = transDropdown.querySelector('.nav-submenu');
  const transChevron = transLink.querySelector('.lucide-chevron-down');
  
  transLink.addEventListener('click', function(e) {
    e.preventDefault();
    const isVisible = transSubmenu.style.display !== 'none';
    transSubmenu.style.display = isVisible ? 'none' : 'block';
    if (transChevron) {
      transChevron.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
    }
  });

  // Close submenu when a submenu item is clicked
  const submenuItems = transSubmenu.querySelectorAll('a');
  submenuItems.forEach(item => {
    item.addEventListener('click', function() {
      transSubmenu.style.display = 'none';
      if (transChevron) {
        transChevron.style.transform = 'rotate(0deg)';
      }
    });
  });
}
</script>
