<!-- ======================== DETAIL MODAL ======================== -->
<div class="modal-overlay" id="detailModal" onclick="closeModalOut(event,'detailModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon"><i class="fas fa-route"></i></div>
            <div class="modal-title">
                <h3 id="dm-id">Detail Trip</h3>
                <p id="dm-date">—</p>
            </div>
            <button class="modal-close" onclick="closeModal('detailModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-user"></i> Pelanggan</div>
                <div class="modal-info-grid">
                    <div class="modal-field"><label>Nama</label><div class="val" id="dm-cust">—</div></div>
                    <div class="modal-field"><label>Telepon</label><div class="val" id="dm-phone">—</div></div>
                    <div class="modal-field"><label>Outlet</label><div class="val" id="dm-outlet">—</div></div>
                    <div class="modal-field"><label>Order Terkait</label><div class="val mono" id="dm-order">—</div></div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-map-marker-alt"></i> Rute</div>
                <div class="modal-info-grid">
                    <div class="modal-field"><label>Alamat Jemput</label><div class="val" id="dm-from">—</div></div>
                    <div class="modal-field"><label>Alamat Antar</label><div class="val" id="dm-to">—</div></div>
                    <div class="modal-field"><label>Jarak</label><div class="val" id="dm-dist">—</div></div>
                    <div class="modal-field"><label>Estimasi Waktu</label><div class="val" id="dm-eta">—</div></div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-motorcycle"></i> Kurir</div>
                <div class="modal-info-grid">
                    <div class="modal-field"><label>Nama Kurir</label><div class="val" id="dm-driver">—</div></div>
                    <div class="modal-field"><label>Kendaraan</label><div class="val" id="dm-vehicle">—</div></div>
                    <div class="modal-field"><label>Layanan</label><div class="val" id="dm-service">—</div></div>
                    <div class="modal-field"><label>Biaya</label><div class="val" style="color:var(--primary);font-weight:700" id="dm-fee">—</div></div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-stream"></i> Progress Trip</div>
                <div class="trip-timeline" id="dm-timeline"></div>
            </div>
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-sticky-note"></i> Catatan</div>
                <div style="padding:.875rem;background:#F9FAFB;border-radius:10px;font-size:.875rem;color:var(--gray)" id="dm-notes"></div>
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="danger" onclick="cancelTrip()" icon="fas fa-times-circle"> Batalkan</x-form.button>
            <x-form.button variant="outline" onclick="closeModal('detailModal')" icon="fas fa-times"> Tutup</x-form.button>
            <x-form.button variant="primary" onclick="openAssignModal()" icon="fas fa-user-plus"> Tugaskan Kurir</x-form.button>
            <x-form.button variant="success" onclick="openStatusModal()" icon="fas fa-exchange-alt"> Update Status</x-form.button>
        </div>
    </div>
</div>

<!-- ======================== STATUS MODAL ======================== -->
<div class="modal-overlay" id="statusModal" onclick="closeModalOut(event,'statusModal')">
    <div class="modal-box" style="max-width:480px">
        <div class="modal-header">
            <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--secondary),var(--success))"><i class="fas fa-exchange-alt"></i></div>
            <div class="modal-title"><h3>Update Status Trip</h3><p id="sm-id">—</p></div>
            <button class="modal-close" onclick="closeModal('statusModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:.875rem;color:var(--gray);margin-bottom:1rem">Pilih status baru untuk trip ini:</p>
            <div class="status-options">
                <div class="status-option" onclick="selectStatus(this,'menunggu')"><div class="so-icon"><i class="fas fa-clock"></i></div><div class="so-label">Menunggu</div></div>
                <div class="status-option" onclick="selectStatus(this,'jemput')"><div class="so-icon"><i class="fas fa-motorcycle"></i></div><div class="so-label">Sedang Jemput</div></div>
                <div class="status-option" onclick="selectStatus(this,'proses')"><div class="so-icon"><i class="fas fa-spinner"></i></div><div class="so-label">Sedang Proses</div></div>
                <div class="status-option" onclick="selectStatus(this,'antar')"><div class="so-icon"><i class="fas fa-shipping-fast"></i></div><div class="so-label">Sedang Antar</div></div>
                <div class="status-option" onclick="selectStatus(this,'selesai')"><div class="so-icon"><i class="fas fa-check-circle"></i></div><div class="so-label">Selesai</div></div>
                <div class="status-option" onclick="selectStatus(this,'batal')"><div class="so-icon"><i class="fas fa-times-circle"></i></div><div class="so-label">Batal</div></div>
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('statusModal')">Batal</x-form.button>
            <x-form.button variant="success" onclick="confirmStatus()" icon="fas fa-check"> Konfirmasi</x-form.button>
        </div>
    </div>
</div>

<!-- ======================== ASSIGN DRIVER MODAL ======================== -->
<div class="modal-overlay" id="assignModal" onclick="closeModalOut(event,'assignModal')">
    <div class="modal-box" style="max-width:460px">
        <div class="modal-header">
            <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--warning),var(--orange))"><i class="fas fa-user-plus"></i></div>
            <div class="modal-title"><h3>Tugaskan Kurir</h3><p>Pilih kurir yang tersedia</p></div>
            <button class="modal-close" onclick="closeModal('assignModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="driverOptions"></div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('assignModal')">Batal</x-form.button>
            <x-form.button variant="primary" onclick="confirmAssign()" icon="fas fa-check"> Tugaskan</x-form.button>
        </div>
    </div>
</div>

<!-- ======================== ADD TRIP MODAL ======================== -->
<div class="modal-overlay" id="addModal" onclick="closeModalOut(event,'addModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--secondary),var(--success))"><i class="fas fa-plus-circle"></i></div>
            <div class="modal-title"><h3>Buat Trip Baru</h3><p>Atur jadwal antar atau jemput</p></div>
            <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom:1.25rem">
                <div class="form-section-title"><i class="fas fa-user"></i> Data Pelanggan</div>
                <div class="form-grid-2">
                    <x-form.input 
                        formField 
                        label="Nama Pelanggan" 
                        id="a-cust" 
                        name="cust" 
                        placeholder="Masukkan nama pelanggan" 
                        icon="fas fa-user" 
                        required 
                    />

                    <x-form.input 
                        formField 
                        label="No. Telepon" 
                        type="tel"
                        id="a-phone" 
                        name="phone" 
                        placeholder="08xxxxxxxxxx" 
                        icon="fas fa-phone" 
                        required 
                    />

                    <x-form.input 
                        formField 
                        label="Order Terkait" 
                        id="a-order" 
                        name="order" 
                        placeholder="cth. ORD-2026-1248" 
                        icon="fas fa-receipt" 
                    />

                    <x-form.select 
                        label="Outlet" 
                        id="a-outlet" 
                        name="outlet_id"
                    >
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>
            </div>
            <div style="margin-bottom:1.25rem">
                <div class="form-section-title"><i class="fas fa-map-marker-alt"></i> Rute</div>
                <div class="form-grid-2">
                    <x-form.textarea 
                        label="Alamat Jemput" 
                        id="a-from" 
                        name="from" 
                        placeholder="Alamat lengkap penjemputan" 
                        icon="fas fa-map-pin" 
                        fullWidth 
                        required 
                    />

                    <x-form.textarea 
                        label="Alamat Antar" 
                        id="a-to" 
                        name="to" 
                        placeholder="Alamat lengkap pengantaran" 
                        icon="fas fa-map-marker-alt" 
                        fullWidth 
                        required 
                    />
                </div>
            </div>
            <div style="margin-bottom:1.25rem">
                <div class="form-section-title"><i class="fas fa-motorcycle"></i> Detail Trip</div>
                <div class="form-grid-2">
                    <x-form.select 
                        label="Layanan" 
                        id="a-service" 
                        name="service"
                    >
                        <option value="Antar Jemput Standar">Antar Jemput Standar</option>
                        <option value="Antar Jemput Express">Antar Jemput Express</option>
                    </x-form.select>

                    <x-form.select 
                        label="Kurir" 
                        id="a-driver" 
                        name="driver"
                    >
                        <option value="">-- Pilih Kurir --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.input 
                        formField 
                        label="Jadwal Trip" 
                        type="datetime-local" 
                        id="a-time" 
                        name="time" 
                        required 
                    />

                    <x-form.input 
                        formField 
                        label="Berat Estimasi" 
                        id="a-weight" 
                        name="weight" 
                        placeholder="cth. 3 kg" 
                        icon="fas fa-weight-hanging" 
                    />

                    <x-form.textarea 
                        label="Catatan" 
                        id="a-notes" 
                        name="notes" 
                        placeholder="Catatan untuk kurir..." 
                        fullWidth 
                    />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('addModal')" icon="fas fa-times"> Batal</x-form.button>
            <x-form.button variant="success" onclick="saveTrip()" icon="fas fa-plus-circle"> Buat Trip</x-form.button>
        </div>
    </div>
</div>
