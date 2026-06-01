<!-- ATUR HARGA MASSAL MODAL -->
<div class="modal-overlay" id="bulkPriceModal" onclick="closeModalOutside(event, 'bulkPriceModal')">
    <div class="modal-box" style="max-width: 500px;">
        <!-- Modal Header -->
        <div class="modal-header">
            <div class="modal-header-icon" style="background: linear-gradient(135deg, var(--primary), var(--purple)); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fas fa-tags"></i>
            </div>
            <div class="modal-title">
                <h3 style="margin: 0; font-size: 1.125rem; font-weight: 700;">Atur Harga Massal</h3>
                <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--gray);">Sesuaikan harga layanan dalam jumlah besar</p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('bulkPriceModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="bulkPriceForm" onsubmit="event.preventDefault(); saveBulkPrice();">
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Kategori -->
                <div class="form-field" style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--gray); text-transform: uppercase; letter-spacing: 0.4px;">Pilih Kategori Layanan <span class="req" style="color: var(--danger);">*</span></label>
                    <select class="form-control" id="bp-category" required style="padding: 0.75rem 1rem; border: 2px solid var(--border); border-radius: 12px; font-size: 0.9375rem; width: 100%;">
                        <option value="all">Semua Kategori</option>
                        <option value="kiloan">Kiloan</option>
                        <option value="satuan">Satuan</option>
                        <option value="paket">Paket</option>
                        <option value="antar">Antar Jemput</option>
                    </select>
                </div>

                <!-- Tipe Aksi -->
                <div class="form-field" style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--gray); text-transform: uppercase; letter-spacing: 0.4px;">Aksi Penyesuaian <span class="req" style="color: var(--danger);">*</span></label>
                    <select class="form-control" id="bp-type" required style="padding: 0.75rem 1rem; border: 2px solid var(--border); border-radius: 12px; font-size: 0.9375rem; width: 100%;">
                        <option value="up">Naikkan Harga</option>
                        <option value="down">Turunkan Harga</option>
                    </select>
                </div>

                <!-- Tipe Penyesuaian -->
                <div class="form-field" style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--gray); text-transform: uppercase; letter-spacing: 0.4px;">Jenis Penyesuaian <span class="req" style="color: var(--danger);">*</span></label>
                    <select class="form-control" id="bp-adjustment-type" required style="padding: 0.75rem 1rem; border: 2px solid var(--border); border-radius: 12px; font-size: 0.9375rem; width: 100%;">
                        <option value="percentage">Persentase (%)</option>
                        <option value="nominal">Nominal Rupiah (Rp)</option>
                    </select>
                </div>

                <!-- Nilai -->
                <div class="form-field" style="margin-bottom: 0.5rem;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--gray); text-transform: uppercase; letter-spacing: 0.4px;">Nilai Penyesuaian <span class="req" style="color: var(--danger);">*</span></label>
                    <input class="form-control" id="bp-value" type="number" placeholder="cth. 10 atau 2000" min="1" required style="padding: 0.75rem 1rem; border: 2px solid var(--border); border-radius: 12px; font-size: 0.9375rem; width: 100%;">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer" style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border); display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" class="modal-btn modal-btn-outline" onclick="closeModal('bulkPriceModal')">
                    Batal
                </button>
                <button type="submit" class="modal-btn modal-btn-primary" id="btnSaveBulkPrice">
                    <div class="spinner"></div>
                    <span class="btn-text"><i class="fas fa-check" style="margin-right: 0.35rem;"></i> Terapkan</span>
                </button>
            </div>
        </form>
    </div>
</div>
