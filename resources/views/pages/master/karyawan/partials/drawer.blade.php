<!-- DETAIL DRAWER -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawerOutside(event)">
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <div class="drawer-header-avatar" id="d-avatar">EM</div>
            <div class="drawer-header-info">
                <div class="drawer-header-name" id="d-name">—</div>
                <div class="drawer-header-id" id="d-id">—</div>
                <div style="margin-top:.35rem" id="d-status-wrap"></div>
            </div>
            <button class="drawer-close" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body">
            <!-- Info -->
            <div class="drawer-section">
                <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Detail Karyawan</div>
                <div class="drawer-info-grid">
                    <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                    <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                    <div class="drawer-field"><label>Tanggal Masuk</label><div class="val" id="d-joined">—</div></div>
                    <div class="drawer-field"><label>Outlet Cabang</label><div class="val" id="d-outlet">—</div></div>
                    <div class="drawer-field"><label>Peran (Role)</label><div class="val" id="d-role">—</div></div>
                    <div class="drawer-field" style="grid-column:span 2"><label>Alamat Lengkap</label><div class="val" id="d-address">—</div></div>
                </div>
            </div>
        </div>
        <div class="drawer-footer">
            <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentEmployee()"><i class="fas fa-trash-alt"></i></button>
            <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
            <button class="drawer-btn drawer-btn-primary" onclick="editCurrentEmployee()"><i class="fas fa-pen"></i> Edit Karyawan</button>
        </div>
    </div>
</div>
