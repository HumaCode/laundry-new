<!-- ===========================
     TOPBAR
============================ -->
<header class="topbar" id="topbar">
    <div class="topbar-title">
        <h2>
            <button class="btn btn-link p-0 me-2 d-md-none text-dark" onclick="toggleMobileSidebar(event)" style="font-size: 1.2rem; border: none; background: none; box-shadow: none;">
                <i class="fas fa-bars"></i>
            </button>
            <i class="fas fa-th-large d-none d-md-inline-block" style="color:var(--primary);font-size:1rem"></i>
            <span>Dashboard</span>
        </h2>
        <div class="breadcrumb-custom d-none d-sm-flex">
            <a href="{{ route('dashboard') }}">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Dashboard</span>
        </div>
    </div>

    <div class="topbar-actions">
        <div class="topbar-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Cari order, pelanggan...">
        </div>

        <button class="topbar-btn" title="Notifikasi">
            <i class="fas fa-bell"></i>
            <span class="topbar-badge">5</span>
        </button>

        <button class="topbar-btn d-none d-sm-flex" title="Pesan">
            <i class="fas fa-comment-alt"></i>
            <span class="topbar-badge">2</span>
        </button>

        <div class="topbar-outlet d-none d-md-flex">
            <i class="fas fa-store"></i>
            <span class="d-none d-sm-inline">Semua Outlet</span>
            <i class="fas fa-chevron-down" style="font-size:0.65rem;opacity:0.6"></i>
        </div>

        <div class="topbar-user" id="topbarUserDropdownToggle">
            <div class="topbar-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>
            <div class="me-1 d-none d-md-block">
                <div class="topbar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="topbar-user-role">
                    {{ ucwords(auth()->user()->roles->first()?->name ?? 'Super Admin') }}
                </div>
            </div>
            <i class="fas fa-chevron-down" style="font-size:0.65rem;opacity:0.6"></i>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu-custom" id="topbarUserDropdown">
                <a href="{{ route('profile.edit') }}" class="dropdown-item-custom">
                    <i class="fas fa-user-circle"></i> Profil
                </a>
                <a href="#" class="dropdown-item-custom">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
                <div class="dropdown-divider-custom"></div>
                <button type="button" class="dropdown-item-custom logout-btn-trigger" onclick="confirmLogout(event)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
    </div>
</header>
