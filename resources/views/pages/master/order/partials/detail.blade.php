<!-- DETAIL MODAL -->
<div class="modal-overlay" id="detailModal" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon"><i class="fas fa-receipt"></i></div>
            <div class="modal-title">
                <h3 id="modalOrderId">Detail Order</h3>
                <p id="modalOrderDate">—</p>
            </div>
            <button class="modal-close" onclick="closeModal('detailModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- Customer Info -->
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-user"></i> Informasi Pelanggan</div>
                <div class="modal-grid">
                    <div class="modal-field"><label>Nama Pelanggan</label><div class="val" id="m-customer">—</div></div>
                    <div class="modal-field"><label>No. Telepon</label><div class="val" id="m-phone">—</div></div>
                    <div class="modal-field"><label>Outlet</label><div class="val" id="m-outlet">—</div></div>
                    <div class="modal-field"><label>Kasir</label><div class="val" id="m-kasir">—</div></div>
                </div>
            </div>
            <!-- Service Info -->
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-concierge-bell"></i> Detail Layanan</div>
                <div class="modal-grid">
                    <div class="modal-field"><label>Layanan</label><div class="val" id="m-service">—</div></div>
                    <div class="modal-field"><label>Tipe</label><div class="val" id="m-type">—</div></div>
                    <div class="modal-field"><label>Jumlah / Berat</label><div class="val" id="m-qty">—</div></div>
                    <div class="modal-field"><label>Harga Satuan</label><div class="val" id="m-price">—</div></div>
                    <div class="modal-field"><label>Total</label><div class="val" id="m-total" style="font-size:1.1rem;color:var(--primary)">—</div></div>
                </div>
            </div>
            <!-- Payment Info -->
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-money-bill-wave"></i> Pembayaran</div>
                <div class="modal-grid">
                    <div class="modal-field"><label>Status Bayar</label><div class="val" id="m-paystatus">—</div></div>
                    <div class="modal-field"><label>Metode</label><div class="val" id="m-paymethod">—</div></div>
                </div>
            </div>
            <!-- Progress Timeline -->
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-stream"></i> Progress Order</div>
                <div class="timeline" id="m-timeline"></div>
            </div>
            <!-- Notes -->
            <div class="modal-section">
                <div class="modal-section-title"><i class="fas fa-sticky-note"></i> Catatan</div>
                <div style="padding:0.875rem;background:#F9FAFB;border-radius:10px;font-size:0.875rem;color:var(--gray)" id="m-notes">Tidak ada catatan.</div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-outline" onclick="closeModal('detailModal')"><i class="fas fa-times"></i> Tutup</button>
            <button class="modal-btn modal-btn-success" onclick="openStatusModal()"><i class="fas fa-exchange-alt"></i> Update Status</button>
            <button class="modal-btn modal-btn-primary" onclick="printOrder()"><i class="fas fa-print"></i> Cetak Nota</button>
        </div>
    </div>
</div>
