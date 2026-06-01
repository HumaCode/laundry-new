<!-- DETAIL DRAWER -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawerOutside(event)">
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <div class="drawer-header-avatar" id="d-avatar">BK</div>
            <div class="drawer-header-info">
                <div class="drawer-header-name" id="d-name">—</div>
                <div class="drawer-header-id" id="d-id">—</div>
                <div style="margin-top:.35rem" id="d-tier-wrap"></div>
            </div>
            <button class="drawer-close" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body">
            <!-- Stats -->
            <div class="drawer-profile-stats">
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalorders">—</div><div class="drawer-stat-lbl">Total Order</div></div>
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalspend">—</div><div class="drawer-stat-lbl">Total Transaksi</div></div>
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-rating">—</div><div class="drawer-stat-lbl">Avg Rating</div></div>
            </div>
            <!-- Info -->
            <div class="drawer-section">
                <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Pribadi</div>
                <div class="drawer-info-grid">
                    <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                    <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                    <div class="drawer-field"><label>Bergabung</label><div class="val" id="d-joined">—</div></div>
                    <div class="drawer-field"><label>Outlet Favorit</label><div class="val" id="d-outlet">—</div></div>
                    <div class="drawer-field" style="grid-column:span 2"><label>Alamat</label><div class="val" id="d-address">—</div></div>
                    <div class="drawer-field"><label>Layanan Favorit</label><div class="val" id="d-favservice">—</div></div>
                    <div class="drawer-field"><label>Avg Order</label><div class="val" id="d-avgorder">—</div></div>
                </div>
            </div>
            <!-- Recent orders -->
            <div class="drawer-section">
                <div class="drawer-section-title"><i class="fas fa-receipt"></i> Order Terbaru</div>
                <div id="d-recent-orders"></div>
            </div>
        </div>
        <div class="drawer-footer">
            <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentCustomer()"><i class="fas fa-trash-alt"></i></button>
            <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
            <button class="drawer-btn drawer-btn-primary" onclick="editCurrentCustomer()"><i class="fas fa-pen"></i> Edit Pelanggan</button>
        </div>
    </div>
</div>
