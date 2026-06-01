<!-- ADD/EDIT MODAL -->
<div class="modal-overlay" id="outletModal" onclick="closeModalOutside(event,'outletModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon" id="modalIcon"><i class="fas fa-store"></i></div>
            <div class="modal-title"><h3 id="modalTitle">Tambah Outlet</h3><p id="modalSubtitle">Isi data outlet baru</p></div>
            <button class="modal-close" onclick="closeModal('outletModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-field">
                    <label>Nama Outlet <span class="req">*</span></label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-name" type="text" placeholder="Masukkan nama outlet"><i class="fas fa-store icon"></i></div>
                </div>
                <div class="form-field">
                    <label>No. Telepon <span class="req">*</span></label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-phone" type="tel" placeholder="08xxxxxxxxxx atau 021-xxxxxx"><i class="fas fa-phone icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="cabang@laundrypro.com"><i class="fas fa-envelope icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Kota Lokasi <span class="req">*</span></label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-city" type="text" placeholder="Contoh: Jakarta Selatan"><i class="fas fa-city icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Manager (PIC)</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-manager" type="text" placeholder="Nama penanggung jawab"><i class="fas fa-user-tie icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Status</label>
                    <select class="form-control" id="f-status">
                        <option value="Aktif">Aktif</option>
                        <option value="Tutup">Tutup</option>
                    </select>
                </div>
                <div class="form-field full">
                    <label>Alamat Lengkap</label>
                    <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="3" placeholder="Alamat lengkap cabang outlet" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                </div>
                <div class="form-field full">
                    <label>Catatan Operasional</label>
                    <input class="form-control" id="f-notes" type="text" placeholder="Catatan operasional khusus (opsional)">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-outline" onclick="closeModal('outletModal')"><i class="fas fa-times"></i> Batal</button>
            <button class="modal-btn modal-btn-primary" id="saveOutletBtn" onclick="saveOutlet()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </div>
</div>
