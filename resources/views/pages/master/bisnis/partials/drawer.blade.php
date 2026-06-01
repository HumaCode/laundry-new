<!-- DETAIL DRAWER -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawerOutside(event)">
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <div class="drawer-header-avatar" id="d-avatar">BS</div>
            <div class="drawer-header-info">
                <div class="drawer-header-name" id="d-name">—</div>
                <div class="drawer-header-id" id="d-id">—</div>
                <div style="margin-top:.35rem" id="d-status-wrap"></div>
            </div>
            <button class="drawer-close" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body">
            <!-- Stats -->
            <div class="drawer-profile-stats">
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-outletcount">—</div><div class="drawer-stat-lbl">Total Outlet</div></div>
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-activeoutlets">—</div><div class="drawer-stat-lbl">Outlet Aktif</div></div>
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-employees">—</div><div class="drawer-stat-lbl">Total Karyawan</div></div>
            </div>
            <!-- Info -->
            <div class="drawer-section">
                <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Bisnis</div>
                <div class="drawer-info-grid">
                    <div class="drawer-field"><label>Pemilik</label><div class="val" id="d-owner">—</div></div>
                    <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                    <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                    <div class="drawer-field"><label>Kota</label><div class="val" id="d-city">—</div></div>
                    <div class="drawer-field" style="grid-column:span 2"><label>Deskripsi</label><div class="val" id="d-description">—</div></div>
                    <div class="drawer-field" style="grid-column:span 2"><label>Alamat</label><div class="val" id="d-address">—</div></div>
                </div>
            </div>
            <!-- Outlet List -->
            <div class="drawer-section">
                <div class="drawer-section-title"><i class="fas fa-store"></i> Daftar Outlet</div>
                <div id="d-outlet-list"></div>
            </div>
        </div>
        <div class="drawer-footer">
            <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentBusiness()"><i class="fas fa-trash-alt"></i></button>
            <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
            <button class="drawer-btn drawer-btn-primary" onclick="editCurrentBusiness()"><i class="fas fa-pen"></i> Edit Bisnis</button>
        </div>
    </div>
</div>
