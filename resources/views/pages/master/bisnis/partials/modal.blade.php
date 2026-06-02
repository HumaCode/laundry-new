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
                <x-form.input 
                    formField 
                    label="Nama Bisnis" 
                    id="f-name" 
                    name="name" 
                    placeholder="Masukkan nama bisnis" 
                    icon="fas fa-building" 
                    required 
                />
                
                <x-form.input 
                    formField 
                    label="Pemilik (Owner)" 
                    id="f-owner" 
                    name="owner" 
                    placeholder="Nama pemilik bisnis" 
                    icon="fas fa-user-tie" 
                />

                <x-form.input 
                    formField 
                    label="No. Telepon" 
                    type="tel"
                    id="f-phone" 
                    name="phone" 
                    placeholder="08xxxxxxxxxx atau 021-xxxxxx" 
                    icon="fas fa-phone" 
                />

                <x-form.input 
                    formField 
                    label="Email" 
                    type="email"
                    id="f-email" 
                    name="email" 
                    placeholder="bisnis@laundrypro.com" 
                    icon="fas fa-envelope" 
                />

                <x-form.input 
                    formField 
                    label="Kota" 
                    id="f-city" 
                    name="city" 
                    placeholder="Contoh: Jakarta Selatan" 
                    icon="fas fa-city" 
                />

                <x-form.select 
                    label="Status" 
                    id="f-status" 
                    name="status"
                >
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </x-form.select>

                <x-form.textarea 
                    label="Deskripsi Singkat" 
                    id="f-description" 
                    name="description" 
                    placeholder="Deskripsi singkat tentang bisnis ini" 
                    icon="fas fa-align-left" 
                    fullWidth 
                />

                <x-form.textarea 
                    label="Alamat Lengkap" 
                    id="f-address" 
                    name="address" 
                    placeholder="Alamat kantor pusat bisnis" 
                    icon="fas fa-map-marker-alt" 
                    fullWidth 
                />
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('businessModal')" icon="fas fa-times"> Batal</x-form.button>
            <x-form.button variant="primary" id="saveBusinessBtn" onclick="saveBusiness()" icon="fas fa-save"> Simpan</x-form.button>
        </div>
    </div>
</div>
