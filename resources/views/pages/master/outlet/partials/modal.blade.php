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
                <x-form.input 
                    formField 
                    label="Nama Outlet" 
                    id="f-name" 
                    name="name" 
                    placeholder="Masukkan nama outlet" 
                    icon="fas fa-store" 
                    required 
                />

                <x-form.input 
                    formField 
                    label="No. Telepon" 
                    type="tel"
                    id="f-phone" 
                    name="phone" 
                    placeholder="08xxxxxxxxxx atau 021-xxxxxx" 
                    icon="fas fa-phone" 
                    required 
                />

                <x-form.input 
                    formField 
                    label="Email" 
                    type="email"
                    id="f-email" 
                    name="email" 
                    placeholder="cabang@laundrypro.com" 
                    icon="fas fa-envelope" 
                />

                <x-form.input 
                    formField 
                    label="Kota Lokasi" 
                    id="f-city" 
                    name="city" 
                    placeholder="Contoh: Jakarta Selatan" 
                    icon="fas fa-city" 
                    required 
                />

                <x-form.input 
                    formField 
                    label="Manager (PIC)" 
                    id="f-manager" 
                    name="manager" 
                    placeholder="Nama penanggung jawab" 
                    icon="fas fa-user-tie" 
                />

                <x-form.select 
                    label="Status" 
                    id="f-status" 
                    name="status"
                >
                    <option value="Aktif">Aktif</option>
                    <option value="Tutup">Tutup</option>
                </x-form.select>

                <x-form.textarea 
                    label="Alamat Lengkap" 
                    id="f-address" 
                    name="address" 
                    rows="3" 
                    placeholder="Alamat lengkap cabang outlet" 
                    icon="fas fa-map-marker-alt" 
                    fullWidth 
                />

                <x-form.input 
                    formField 
                    label="Catatan Operasional" 
                    id="f-notes" 
                    name="notes" 
                    placeholder="Catatan operasional khusus (opsional)" 
                    fullWidth 
                />
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('outletModal')" icon="fas fa-times"> Batal</x-form.button>
            <x-form.button variant="primary" id="saveOutletBtn" onclick="saveOutlet()" icon="fas fa-save"> Simpan</x-form.button>
        </div>
    </div>
</div>
