<!-- ADD/EDIT MODAL -->
<div class="modal-overlay" id="custModal" onclick="closeModalOutside(event,'custModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon" id="modalIcon"><i class="fas fa-user-plus"></i></div>
            <div class="modal-title"><h3 id="modalTitle">Tambah Pelanggan</h3><p id="modalSubtitle">Isi data pelanggan baru</p></div>
            <button class="modal-close" onclick="closeModal('custModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-field">
                    <label>Nama Lengkap <span class="req">*</span></label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-name" type="text" placeholder="Masukkan nama lengkap"><i class="fas fa-user icon"></i></div>
                </div>
                <div class="form-field">
                    <label>No. Telepon <span class="req">*</span></label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-phone" type="tel" placeholder="08xxxxxxxxxx"><i class="fas fa-phone icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="email@contoh.com"><i class="fas fa-envelope icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Tanggal Lahir</label>
                    <div class="input-icon-wrap"><input class="form-control" id="f-dob" type="date"><i class="fas fa-birthday-cake icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" id="f-gender">
                        <option value="">-- Pilih --</option>
                        <option>Laki-laki</option>
                        <option>Perempuan</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Outlet Favorit</label>
                    <select class="form-control" id="f-outlet">
                        <option value="">-- Pilih --</option>
                        @foreach($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field full">
                    <label>Alamat</label>
                    <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="3" placeholder="Alamat lengkap pelanggan" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                </div>
                <div class="form-field">
                    <label>Tier</label>
                    <select class="form-control" id="f-tier">
                        <option value="Baru">Baru</option>
                        <option value="Reguler">Reguler</option>
                        <option value="Premium">Premium</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Catatan</label>
                    <input class="form-control" id="f-notes" type="text" placeholder="Catatan khusus (opsional)">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-outline" onclick="closeModal('custModal')"><i class="fas fa-times"></i> Batal</button>
            <button class="modal-btn modal-btn-primary" onclick="saveCustomer()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </div>
</div>
