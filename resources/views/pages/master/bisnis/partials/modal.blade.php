<!-- ADD/EDIT MODAL -->
<div class="modal-overlay" id="businessModal" onclick="closeModalOutside(event,'businessModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon" id="modalIcon"><i class="fas fa-building"></i></div>
            <div class="modal-title"><h3 id="modalTitle">Tambah Bisnis</h3><p id="modalSubtitle">Isi data bisnis baru</p></div>
            <button class="modal-close" onclick="closeModal('businessModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-field">
                    <label>Nama Bisnis <span class="req">*</span></label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-name" type="text" placeholder="Masukkan nama bisnis"><i class="fas fa-building icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Pemilik (Owner)</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-owner" type="text" placeholder="Nama pemilik bisnis"><i class="fas fa-user-tie icon"></i></div>
                </div>
                <div class="form-field">
                    <label>No. Telepon</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-phone" type="tel" placeholder="08xxxxxxxxxx atau 021-xxxxxx"><i class="fas fa-phone icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="bisnis@laundrypro.com"><i class="fas fa-envelope icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Kota</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-city" type="text" placeholder="Contoh: Jakarta Selatan"><i class="fas fa-city icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Status</label>
                    <select class="form-control" id="f-status">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-field full">
                    <label>Deskripsi Singkat</label>
                    <div class="input-icon-wrap"><textarea class="form-control" id="f-description" rows="2" placeholder="Deskripsi singkat tentang bisnis ini" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-align-left icon" style="top:1rem;transform:none"></i></div>
                </div>
                <div class="form-field full">
                    <label>Alamat Lengkap</label>
                    <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="2" placeholder="Alamat kantor pusat bisnis" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-outline" onclick="closeModal('businessModal')"><i class="fas fa-times"></i> Batal</button>
            <button class="modal-btn modal-btn-primary" id="saveBusinessBtn" onclick="saveBusiness()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </div>
</div>
