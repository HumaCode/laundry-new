@php
    if (!function_exists('filterKata')) {
        function filterKata($str) {
            return $str;
        }
    }

    $getIcon = function($icon) {
        $map = [
            'shopping-cart' => 'shopping-cart',
            'users' => 'users',
            'home' => 'home',
            'tag' => 'tag',
            'truck' => 'truck',
            'user-check' => 'user-check',
            'archive' => 'archive',
            'file-text' => 'file-alt',
            'credit-card' => 'credit-card',
            'gift' => 'gift',
            'shield' => 'shield-alt',
            'key' => 'key',
            'menu' => 'bars',
            'user' => 'user',
            'settings' => 'cog',
        ];
        return $map[$icon] ?? $icon;
    };

    $isMenuActive = function($menu) {
        return request()->is($menu->url) || request()->is($menu->url . '/*');
    };

    $menus = menus();
    $unverifiedCount = $unverifiedCount ?? 0;
    $unverifiedMemberCount = $unverifiedMemberCount ?? 0;
    $currentPath = request()->path();
@endphp

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
        <div class="nav-section-label">UTAMA</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-th-large"></i></div>
            <span class="nav-label">Dashboard</span>
        </a>

        <!-- Dynamic Menus from Database -->
        @foreach ($menus as $category => $items)
            <div class="nav-section-label" title="{{ $category }}">{{ strtoupper($category) }}</div>

            @foreach ($items as $mm)
                @php
                    $hasSub = count($mm->subMenus ?? []) > 0;
                    $isActive = $isMenuActive($mm);
                    $menuIcon = $getIcon($mm->icon);
                    $menuName = filterKata(ucwords($mm->name));
                @endphp

                <div class="nav-item-wrapper">
                    @if ($hasSub)
                        <a href="javascript:void(0)" 
                           class="nav-item has-submenu {{ $isActive ? 'active' : '' }}" 
                           data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $menuName }}"
                           aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                            <div class="nav-icon"><i class="fas fa-{{ $menuIcon }}"></i></div>
                            <span class="nav-label">{{ $menuName }}</span>
                            @if($unverifiedCount > 0 && (strtolower($menuName) == 'permohonan masuk' || strtolower($menuName) == 'permohonan' || trim($mm->url, '/') == 'administrator/permohonan' || trim($mm->url, '/') == 'permohonan'))
                                <span class="badge rounded-pill bg-warning text-dark ms-2 badge-pulse-warning" style="font-size: 0.7rem; padding: 0.25em 0.6em; margin-right: 0.5rem; background-color: #f59e0b !important; color: #fff !important; font-weight: 700; box-shadow: 0 2px 6px rgba(245,158,11,0.4);">
                                    {{ $unverifiedCount }}
                                </span>
                            @endif
                            @if($unverifiedMemberCount > 0 && (strtolower($menuName) == 'pemohon' || strtolower($menuName) == 'member' || trim($mm->url, '/') == 'administrator/member' || trim($mm->url, '/') == 'member'))
                                <span class="badge rounded-pill bg-danger ms-2 badge-pulse-danger" style="font-size: 0.7rem; padding: 0.25em 0.6em; margin-right: 0.5rem; background-color: #ef4444 !important; color: #fff !important; font-weight: 700; box-shadow: 0 2px 6px rgba(239,68,68,0.4);">
                                    {{ $unverifiedMemberCount }}
                                </span>
                            @endif
                            <i class="fas fa-chevron-down ms-auto submenu-caret" 
                               style="font-size: .7rem; transition: transform .3s; {{ $isActive ? 'transform: rotate(180deg);' : '' }}"></i>
                        </a>
                        <ul class="nav-submenu {{ $isActive ? 'show' : '' }}">
                            @foreach ($mm->subMenus as $sm)
                                @php
                                    $smUrl = trim(filterKata($sm->url), '/');
                                    $isSubActive = ($currentPath === $smUrl || str_starts_with($currentPath, $smUrl . '/'));
                                    $smName = filterKata(ucwords($sm->name));
                                    $smIconRaw = filterKata($sm->icon);
                                    $smIcon = empty($smIconRaw) ? 'circle' : $getIcon($sm->icon);
                                @endphp
                                <li class="nav-sub-item">
                                    <a href="{{ url($sm->url) }}" 
                                       class="nav-sub-link {{ $isSubActive ? 'active' : '' }}">
                                        <div class="nav-sub-icon"><i class="fas fa-{{ $smIcon }}"></i></div>
                                        <span class="nav-sub-label">{{ $smName }}</span>
                                        @if($unverifiedCount > 0 && (strtolower($smName) == 'permohonan masuk' || strtolower($smName) == 'permohonan' || trim($sm->url, '/') == 'administrator/permohonan' || trim($sm->url, '/') == 'permohonan'))
                                            <span class="badge rounded-pill bg-warning text-dark ms-auto badge-pulse-warning" style="font-size: 0.65rem; padding: 0.2em 0.5em; background-color: #f59e0b !important; color: #fff !important; font-weight: 700; box-shadow: 0 2px 6px rgba(245,158,11,0.4);">
                                                {{ $unverifiedCount }}
                                            </span>
                                        @endif
                                        @if($unverifiedMemberCount > 0 && (strtolower($smName) == 'pemohon' || strtolower($smName) == 'member' || trim($sm->url, '/') == 'administrator/member' || trim($sm->url, '/') == 'member'))
                                            <span class="badge rounded-pill bg-danger ms-auto badge-pulse-danger" style="font-size: 0.65rem; padding: 0.2em 0.5em; background-color: #ef4444 !important; color: #fff !important; font-weight: 700; box-shadow: 0 2px 6px rgba(239,68,68,0.4);">
                                                {{ $unverifiedMemberCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ url($mm->url) }}" 
                           class="nav-item {{ $isActive ? 'active' : '' }}" 
                           data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $menuName }}">
                            <div class="nav-icon"><i class="fas fa-{{ $menuIcon }}"></i></div>
                            <span class="nav-label">{{ $menuName }}</span>
                            @if($unverifiedCount > 0 && (strtolower($menuName) == 'permohonan masuk' || strtolower($menuName) == 'permohonan' || trim($mm->url, '/') == 'administrator/permohonan' || trim($mm->url, '/') == 'permohonan'))
                                <span class="badge rounded-pill bg-warning text-dark ms-auto badge-pulse-warning" style="font-size: 0.7rem; padding: 0.25em 0.6em; background-color: #f59e0b !important; color: #fff !important; font-weight: 700; box-shadow: 0 2px 6px rgba(245,158,11,0.4);">
                                    {{ $unverifiedCount }}
                                </span>
                            @endif
                            @if($unverifiedMemberCount > 0 && (strtolower($menuName) == 'pemohon' || strtolower($menuName) == 'member' || trim($mm->url, '/') == 'administrator/member' || trim($mm->url, '/') == 'member'))
                                <span class="badge rounded-pill bg-danger ms-auto badge-pulse-danger" style="font-size: 0.7rem; padding: 0.25em 0.6em; background-color: #ef4444 !important; color: #fff !important; font-weight: 700; box-shadow: 0 2px 6px rgba(239,68,68,0.4);">
                                    {{ $unverifiedMemberCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                </div>
            @endforeach
        @endforeach
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
