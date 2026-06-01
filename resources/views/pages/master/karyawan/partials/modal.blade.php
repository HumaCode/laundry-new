<!-- ADD/EDIT MODAL -->
<div class="modal-overlay" id="employeeModal" onclick="closeModalOutside(event,'employeeModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon" id="modalIcon"><i class="fas fa-user-check"></i></div>
            <div class="modal-title"><h3 id="modalTitle">Tambah Karyawan</h3><p id="modalSubtitle">Isi data karyawan baru</p></div>
            <button class="modal-close" onclick="closeModal('employeeModal')"><i class="fas fa-times"></i></button>
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
                    <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="karyawan@laundrypro.com"><i class="fas fa-envelope icon"></i></div>
                </div>
                <div class="form-field">
                    <label>Outlet Penempatan <span class="req">*</span></label>
                    <select class="form-control" id="f-outlet">
                        <option value="">Pilih Cabang Outlet</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label>Peran (Role) <span class="req">*</span></label>
                    <select class="form-control" id="f-role">
                        <option value="">Pilih atau Ketik Peran</option>
                        <option value="Kepala Outlet">Kepala Outlet</option>
                        <option value="Kasir">Kasir</option>
                        <option value="Kurir">Kurir</option>
                        <option value="Pencuci">Pencuci</option>
                        <option value="Penyetrika">Penyetrika</option>
                        <option value="Staff Admin">Staff Admin</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Status</label>
                    <select class="form-control" id="f-status">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Tanggal Masuk Bekerja</label>
                    <div class="flatpickr-wrap">
                        <span class="flatpickr-calendar-icon"><i class="fas fa-calendar-alt"></i></span>
                        <input class="form-control flatpickr-input-custom" id="f-joined_at"
                            type="text" placeholder="Pilih tanggal bergabung" readonly>
                        <button type="button" class="flatpickr-clear-btn" id="f-joined_at-clear"
                            onclick="clearDate()" title="Hapus tanggal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="form-field full">
                    <label>Alamat Lengkap</label>
                    <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="3" placeholder="Alamat lengkap tempat tinggal" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-outline" onclick="closeModal('employeeModal')"><i class="fas fa-times"></i> Batal</button>
            <button class="modal-btn modal-btn-primary" id="saveEmployeeBtn" onclick="saveEmployee()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </div>
</div>
