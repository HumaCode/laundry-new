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
                    placeholder="email@contoh.com" 
                    icon="fas fa-envelope" 
                />

                <x-form.input 
                    formField 
                    label="Tanggal Lahir" 
                    type="date"
                    id="f-dob" 
                    name="dob" 
                    icon="fas fa-birthday-cake" 
                />

                <x-form.select 
                    label="Jenis Kelamin" 
                    id="f-gender" 
                    name="gender"
                >
                    <option value="">-- Pilih --</option>
                    <option>Laki-laki</option>
                    <option>Perempuan</option>
                </x-form.select>

                <x-form.select 
                    label="Outlet Favorit" 
                    id="f-outlet" 
                    name="outlet_id"
                >
                    <option value="">-- Pilih --</option>
                    @foreach($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </x-form.select>

                <x-form.textarea 
                    label="Alamat" 
                    id="f-address" 
                    name="address" 
                    rows="3" 
                    placeholder="Alamat lengkap pelanggan" 
                    icon="fas fa-map-marker-alt" 
                    fullWidth 
                />

                <x-form.select 
                    label="Tier" 
                    id="f-tier" 
                    name="tier"
                >
                    <option value="Baru">Baru</option>
                    <option value="Reguler">Reguler</option>
                    <option value="Premium">Premium</option>
                    <option value="VIP">VIP</option>
                </x-form.select>

                <x-form.input 
                    formField 
                    label="Catatan" 
                    id="f-notes" 
                    name="notes" 
                    placeholder="Catatan khusus (opsional)" 
                />
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('custModal')" icon="fas fa-times"> Batal</x-form.button>
            <x-form.button variant="primary" onclick="saveCustomer()" icon="fas fa-save"> Simpan</x-form.button>
        </div>
    </div>
</div>
