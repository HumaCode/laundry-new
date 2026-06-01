<!-- DETAIL DRAWER -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawerOutside(event)">
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <div class="drawer-header-avatar" id="d-avatar">OP</div>
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
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalorders">—</div><div class="drawer-stat-lbl">Total Order</div></div>
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalrevenue">—</div><div class="drawer-stat-lbl">Total Omset</div></div>
                <div class="drawer-stat"><div class="drawer-stat-val" id="d-staffcount">—</div><div class="drawer-stat-lbl">Jumlah Staff</div></div>
            </div>
            <!-- Info -->
            <div class="drawer-section">
                <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Cabang</div>
                <div class="drawer-info-grid">
                    <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                    <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                    <div class="drawer-field"><label>Tanggal Dibuka</label><div class="val" id="d-joined">—</div></div>
                    <div class="drawer-field"><label>Kota</label><div class="val" id="d-city">—</div></div>
                    <div class="drawer-field" style="grid-column:span 2"><label>Alamat Lengkap</label><div class="val" id="d-address">—</div></div>
                    <div class="drawer-field"><label>Manager (PIC)</label><div class="val" id="d-manager">—</div></div>
                </div>
            </div>
            <!-- Staff List -->
            <div class="drawer-section">
                <div class="drawer-section-title"><i class="fas fa-users-cog"></i> Daftar Karyawan</div>
                <div id="d-recent-employees"></div>
            </div>
        </div>
        <div class="drawer-footer">
            <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentOutlet()"><i class="fas fa-trash-alt"></i></button>
            <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
            <button class="drawer-btn drawer-btn-primary" onclick="editCurrentOutlet()"><i class="fas fa-pen"></i> Edit Outlet</button>
        </div>
    </div>
</div>
