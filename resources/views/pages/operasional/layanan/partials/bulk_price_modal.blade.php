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
                <x-form.select 
                    label="Pilih Kategori Layanan" 
                    id="bp-category" 
                    name="category"
                    required
                    fullWidth
                >
                    <option value="all">Semua Kategori</option>
                    <option value="kiloan">Kiloan</option>
                    <option value="satuan">Satuan</option>
                    <option value="paket">Paket</option>
                    <option value="antar">Antar Jemput</option>
                </x-form.select>

                <!-- Tipe Aksi -->
                <x-form.select 
                    label="Aksi Penyesuaian" 
                    id="bp-type" 
                    name="type"
                    required
                    fullWidth
                >
                    <option value="up">Naikkan Harga</option>
                    <option value="down">Turunkan Harga</option>
                </x-form.select>

                <!-- Tipe Penyesuaian -->
                <x-form.select 
                    label="Jenis Penyesuaian" 
                    id="bp-adjustment-type" 
                    name="adjustment_type"
                    required
                    fullWidth
                >
                    <option value="percentage">Persentase (%)</option>
                    <option value="nominal">Nominal Rupiah (Rp)</option>
                </x-form.select>

                <!-- Nilai -->
                <x-form.input 
                    formField 
                    label="Nilai Penyesuaian" 
                    type="number"
                    id="bp-value" 
                    name="value" 
                    placeholder="cth. 10 atau 2000" 
                    min="1"
                    required 
                    fullWidth
                />
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <x-form.button type="button" variant="outline" onclick="closeModal('bulkPriceModal')">Batal</x-form.button>
                <x-form.button type="submit" variant="primary" id="btnSaveBulkPrice">
                    <div class="spinner"></div>
                    <span class="btn-text"><i class="fas fa-check" style="margin-right: 0.35rem;"></i> Terapkan</span>
                </x-form.button>
            </div>
        </form>
    </div>
</div>
