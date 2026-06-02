<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/services.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/services.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Layanan & Harga</h2>
            <p>Kelola seluruh layanan laundry, harga satuan, dan paket langganan</p>
        </div>
        <div class="page-header-actions">
            <button class="btn-page btn-page-outline" onclick="showToast('Fitur import segera hadir', 'info', 'Import')"><i class="fas fa-file-import"></i> Import</button>
            <button class="btn-page btn-page-outline" onclick="openBulkPriceModal()"><i class="fas fa-tags"></i> Atur Harga Massal</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Layanan</button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <x-stat-card 
            theme="c1"
            icon="list-alt"
            value="{{ $stats['total'] }}"
            valueId="statTotal"
            title="Total Layanan"
            footerText="Semua kategori layanan"
            delayClass="d1"
        />
        <x-stat-card 
            theme="c2"
            icon="check-circle"
            value="{{ $stats['active'] }}"
            valueId="statAktif"
            title="Layanan Aktif"
            footerText="Tersedia untuk pelanggan"
            delayClass="d2"
        />
        <x-stat-card 
            theme="c3"
            icon="fire-alt"
            value="{{ $stats['terlaris'] }}"
            valueId="statTerlaris"
            title="Layanan Terlaris"
            footerText="Order terbanyak"
            delayClass="d3"
            valueClass="text-long"
        />
        <x-stat-card 
            theme="c4"
            icon="wallet"
            value="{{ $stats['revenue_max'] }}"
            valueId="statRevenue"
            title="Kontribusi Tertinggi"
            footerText="Layanan penghasil terbesar"
            delayClass="d4"
            valueClass="text-long"
        />
    </div>

    <!-- Category Tabs -->
    <div class="tabs-bar fade-in" id="tabsBar"></div>

    <!-- Service Cards Grid -->
    <div id="servicesContainer" class="services-grid fade-in">
        <!-- Initial Skeleton Loading -->
        @for ($i = 0; $i < 6; $i++)
            <div class="service-card" style="pointer-events:none;opacity:0.7">
                <div class="sc-body">
                    <div class="sc-top" style="margin-bottom:1.5rem">
                        <div class="skeleton skeleton-circle" style="width:52px;height:52px;border-radius:14px;"></div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.35rem">
                            <div class="skeleton" style="width:60px;height:22px;border-radius:6px"></div>
                            <div class="skeleton" style="width:80px;height:18px;border-radius:6px"></div>
                        </div>
                    </div>
                    <div class="skeleton skeleton-text medium" style="height:16px;width:150px;margin-bottom:0.5rem"></div>
                    <div class="skeleton skeleton-text long" style="height:12px;width:100%;margin-bottom:1.5rem"></div>
                    <div class="skeleton" style="height:80px;border-radius:12px;width:100%;margin-bottom:1.5rem"></div>
                </div>
                <div class="sc-footer" style="gap:0.5rem">
                    <div class="skeleton" style="flex:1;height:35px;border-radius:10px"></div>
                    <div class="skeleton" style="flex:1;height:35px;border-radius:10px"></div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Empty State -->
    <div class="empty-state" id="emptyState" style="display: none;">
        <div class="empty-icon"><i class="fas fa-concierge-bell"></i></div>
        <div class="empty-title">Tidak ada layanan ditemukan</div>
        <div class="empty-desc">Coba ubah filter pencarian Anda</div>
    </div>

    <!-- Pagination Footer -->
    <div class="pagination-bar fade-in" id="paginationBar" style="background: white; border-radius: 20px; border: 1px solid var(--border); margin-top: 1.5rem; display: none;">
        <div class="pagination-info">Menampilkan <span id="showCount">0</span> dari <span id="totalCount">0</span> layanan (Halaman <strong id="currentPage">1</strong> dari <strong id="totalPages">1</strong>)</div>
        <div class="pagination-controls" id="paginationControls"></div>
    </div>

    <!-- Float Button -->
    <div class="float-btn-container">
        <button class="float-btn" id="scrollTopBtn" onclick="scrollToTop(event)">
            <div class="float-btn-ring"></div>
            <i class="fas fa-arrow-up"></i>
            <span class="float-btn-tooltip">Kembali ke Atas</span>
        </button>
    </div>

    <!-- Modals Partials -->
    @include('pages.operasional.layanan.partials.modal')
    @include('pages.operasional.layanan.partials.bulk_price_modal')
</x-app-layout>
