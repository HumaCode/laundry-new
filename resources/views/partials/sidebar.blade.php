<!-- ===========================
     SIDEBAR
============================ -->
<aside class="sidebar" id="sidebar">
    <!-- Toggle button -->
    <div class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-chevron-left"></i>
    </div>

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-tshirt"></i></div>
        <div class="brand-text">
            <span class="brand-name">LaundryPro</span>
            <span class="brand-sub">Admin Panel</span>
        </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-th-large"></i></div>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-receipt"></i></div>
            <span class="nav-label">Semua Order</span>
            <span class="nav-badge">12</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-users"></i></div>
            <span class="nav-label">Pelanggan</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-store"></i></div>
            <span class="nav-label">Outlet</span>
        </a>

        <div class="nav-section-label">Operasional</div>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-concierge-bell"></i></div>
            <span class="nav-label">Layanan & Harga</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-truck"></i></div>
            <span class="nav-label">Antar Jemput</span>
            <span class="nav-badge">3</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-user-tie"></i></div>
            <span class="nav-label">Karyawan</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-boxes"></i></div>
            <span class="nav-label">Inventaris</span>
        </a>

        <div class="nav-section-label">Keuangan</div>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
            <span class="nav-label">Laporan</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <span class="nav-label">Pembayaran</span>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-percent"></i></div>
            <span class="nav-label">Promo & Diskon</span>
        </a>

        <div class="nav-section-label">Sistem</div>
        <a href="#" class="nav-item">
            <div class="nav-icon"><i class="fas fa-cog"></i></div>
            <span class="nav-label">Pengaturan</span>
        </a>
    </nav>

    <!-- Sidebar User -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
            <div class="sidebar-user-role">
                {{ ucwords(auth()->user()->roles->first()?->name ?? 'Super Admin') }}
            </div>
        </div>
        <button class="sidebar-user-btn" onclick="handleLogout()">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</aside>
