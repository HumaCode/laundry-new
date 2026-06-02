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
                <x-form.input 
                    formField 
                    label="Nama Lengkap" 
                    id="f-name" 
                    name="name" 
                    placeholder="Masukkan nama lengkap" 
                    icon="fas fa-user" 
                    required 
                />

                <x-form.input 
                    formField 
                    label="No. Telepon" 
                    type="tel"
                    id="f-phone" 
                    name="phone" 
                    placeholder="08xxxxxxxxxx" 
                    icon="fas fa-phone" 
                    required 
                />

                <x-form.input 
                    formField 
                    label="Email" 
                    type="email"
                    id="f-email" 
                    name="email" 
                    placeholder="karyawan@laundrypro.com" 
                    icon="fas fa-envelope" 
                />

                <x-form.select 
                    label="Outlet Penempatan" 
                    id="f-outlet" 
                    name="outlet_id"
                    required
                >
                    <option value="">Pilih Cabang Outlet</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </x-form.select>

                <x-form.select 
                    label="Peran (Role)" 
                    id="f-role" 
                    name="role"
                    required
                >
                    <option value="">Pilih atau Ketik Peran</option>
                    <option value="Kepala Outlet">Kepala Outlet</option>
                    <option value="Kasir">Kasir</option>
                    <option value="Kurir">Kurir</option>
                    <option value="Pencuci">Pencuci</option>
                    <option value="Penyetrika">Penyetrika</option>
                    <option value="Staff Admin">Staff Admin</option>
                </x-form.select>

                <x-form.select 
                    label="Status" 
                    id="f-status" 
                    name="status"
                >
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </x-form.select>

                <x-form.date 
                    label="Tanggal Masuk Bekerja" 
                    id="f-joined_at" 
                    name="joined_at" 
                    placeholder="Pilih tanggal bergabung" 
                    clearCallback="clearDate()" 
                />

                <x-form.textarea 
                    label="Alamat Lengkap" 
                    id="f-address" 
                    name="address" 
                    rows="3" 
                    placeholder="Alamat lengkap tempat tinggal" 
                    icon="fas fa-map-marker-alt" 
                    fullWidth 
                />
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('employeeModal')" icon="fas fa-times"> Batal</x-form.button>
            <x-form.button variant="primary" id="saveEmployeeBtn" onclick="saveEmployee()" icon="fas fa-save"> Simpan</x-form.button>
        </div>
    </div>
</div>
