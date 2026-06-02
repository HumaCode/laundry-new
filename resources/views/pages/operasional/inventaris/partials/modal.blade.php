<!-- ====================== ADD / EDIT MODAL ====================== -->
<div class="modal-overlay" id="itemModal" onclick="closeModalOut(event,'itemModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon" id="itemModalIcon" style="background:linear-gradient(135deg,var(--primary),var(--purple))"><i class="fas fa-boxes"></i></div>
            <div class="modal-title"><h3 id="itemModalTitle">Tambah Barang</h3><p id="itemModalSub">Isi detail barang inventaris baru</p></div>
            <button class="modal-close" onclick="closeModal('itemModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-section-title"><i class="fas fa-info-circle"></i> Informasi Barang</div>
            <div class="form-grid-2">
                <x-form.input 
                    formField 
                    label="Nama Barang" 
                    id="f-name" 
                    name="name" 
                    placeholder="cth. Deterjen Attack Cair" 
                    icon="fas fa-tag" 
                    required 
                />

                <x-form.input 
                    formField 
                    label="Kode Barang" 
                    id="f-code" 
                    name="code" 
                    placeholder="cth. DTJ-001" 
                    icon="fas fa-barcode" 
                />

                <x-form.select 
                    label="Kategori" 
                    id="f-category" 
                    name="category"
                    required
                >
                    <option>Deterjen & Kimia</option>
                    <option>Pewangi & Softener</option>
                    <option>Plastik & Kemasan</option>
                    <option>Peralatan Cuci</option>
                    <option>Peralatan Setrika</option>
                    <option>Kebersihan Outlet</option>
                    <option>ATK & Administrasi</option>
                </x-form.select>

                <x-form.input 
                    formField 
                    label="Merek / Supplier" 
                    id="f-brand" 
                    name="brand" 
                    placeholder="cth. Attack / CV Maju Jaya" 
                    icon="fas fa-building" 
                />

                <x-form.select 
                    label="Ikon (Emoji)" 
                    id="f-emoji" 
                    name="emoji"
                >
                    <option value="📦">📦 Box / Kemasan</option>
                    <option value="🧴">🧴 Botol / Deterjen</option>
                    <option value="🌸">🌸 Bunga / Pewangi</option>
                    <option value="🪣">🪣 Ember / Peralatan Cuci</option>
                    <option value="🔌">🔌 Colokan / Setrika</option>
                    <option value="🧹">🧹 Sapu / Kebersihan</option>
                    <option value="📋">📋 Papan Ujian / ATK</option>
                    <option value="🫧">🫧 Gelembung Sabun</option>
                    <option value="🧺">🧺 Keranjang Baju</option>
                    <option value="💧">💧 Tetes Air</option>
                    <option value="🧼">🧼 Sabun Batang</option>
                    <option value="👕">👕 Kaos / Pakaian</option>
                    <option value="🧤">🧤 Sarung Tangan</option>
                </x-form.select>

                <x-form.select 
                    label="Warna Ikon" 
                    id="f-color" 
                    name="color"
                >
                    <option value="#6366F1">Ungu (Deterjen)</option>
                    <option value="#10B981">Hijau (Kimia)</option>
                    <option value="#F59E0B">Kuning (Peralatan)</option>
                    <option value="#EC4899">Pink (Pewangi)</option>
                    <option value="#3B82F6">Biru (Kemasan)</option>
                    <option value="#F97316">Oranye (ATK)</option>
                </x-form.select>

                <x-form.textarea 
                    label="Deskripsi" 
                    id="f-desc" 
                    name="desc" 
                    placeholder="Deskripsi singkat atau spesifikasi barang..." 
                    fullWidth 
                />
            </div>

            <div class="form-section-title"><i class="fas fa-layer-group"></i> Stok & Harga</div>
            <div class="form-grid-3">
                <x-form.input 
                    formField 
                    label="Stok Awal" 
                    type="number"
                    id="f-stock" 
                    name="stock" 
                    placeholder="0" 
                    min="0"
                    required 
                />

                <x-form.input 
                    formField 
                    label="Stok Minimum" 
                    type="number"
                    id="f-minStock" 
                    name="min_stock" 
                    placeholder="0" 
                    min="0"
                />

                <x-form.input 
                    formField 
                    label="Stok Maksimum" 
                    type="number"
                    id="f-maxStock" 
                    name="max_stock" 
                    placeholder="0" 
                    min="0"
                />

                <x-form.select 
                    label="Satuan" 
                    id="f-unit" 
                    name="unit"
                >
                    <option>liter</option>
                    <option>kg</option>
                    <option>pcs</option>
                    <option>botol</option>
                    <option>dus</option>
                    <option>pack</option>
                    <option>roll</option>
                    <option>lembar</option>
                </x-form.select>

                <x-form.input 
                    formField 
                    label="Harga Beli/Satuan" 
                    type="number"
                    id="f-price" 
                    name="price" 
                    placeholder="0" 
                    min="0"
                />

                <x-form.select 
                    label="Outlet" 
                    id="f-outlet" 
                    name="outlet_id"
                    required
                >
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('itemModal')" icon="fas fa-times"> Batal</x-form.button>
            <x-form.button variant="primary" id="btnSaveItem" onclick="saveItem()"><i class="fas fa-save btn-icon"></i> <span class="btn-text">Simpan</span></x-form.button>
        </div>
    </div>
</div>

<!-- ====================== DETAIL MODAL ====================== -->
<div class="modal-overlay" id="detailModal" onclick="closeModalOut(event,'detailModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-icon" id="dm-icon" style="background:linear-gradient(135deg,var(--primary),var(--purple));font-size:1.5rem">📦</div>
            <div class="modal-title"><h3 id="dm-name">—</h3><p id="dm-code">—</p></div>
            <button class="modal-close" onclick="closeModal('detailModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom:1.25rem">
                <div class="form-section-title"><i class="fas fa-info-circle"></i> Informasi Barang</div>
                <div class="detail-info-grid">
                    <div class="detail-field"><label>Kategori</label><div class="val" id="dm-cat">—</div></div>
                    <div class="detail-field"><label>Merek / Supplier</label><div class="val" id="dm-brand">—</div></div>
                    <div class="detail-field"><label>Outlet</label><div class="val" id="dm-outlet">—</div></div>
                    <div class="detail-field"><label>Satuan</label><div class="val" id="dm-unit">—</div></div>
                    <div class="detail-field" style="grid-column:span 2"><label>Deskripsi</label><div class="val" id="dm-desc" style="font-weight:400;color:var(--gray)">—</div></div>
                </div>
            </div>
            <div style="margin-bottom:1.25rem">
                <div class="form-section-title"><i class="fas fa-layer-group"></i> Detail Stok</div>
                <div class="detail-info-grid">
                    <div class="detail-field"><label>Stok Saat Ini</label><div class="val" id="dm-stock" style="font-size:1.5rem;color:var(--primary)">—</div></div>
                    <div class="detail-field"><label>Status</label><div id="dm-status">—</div></div>
                    <div class="detail-field"><label>Stok Minimum</label><div class="val" id="dm-min">—</div></div>
                    <div class="detail-field"><label>Stok Maksimum</label><div class="val" id="dm-max">—</div></div>
                    <div class="detail-field"><label>Harga Beli</label><div class="val" id="dm-price">—</div></div>
                    <div class="detail-field"><label>Nilai Stok</label><div class="val" id="dm-value" style="color:var(--primary);font-weight:800">—</div></div>
                    <div class="detail-field"><label>Restock Terakhir</label><div class="val" id="dm-last-restock">—</div></div>
                    <div class="detail-field"><label>Jumlah Restock Terakhir</label><div class="val" id="dm-last-qty">—</div></div>
                </div>
            </div>
            <div>
                <div class="form-section-title"><i class="fas fa-history"></i> Riwayat Restock</div>
                <div class="restock-list" id="dm-restock-history"></div>
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="danger" onclick="deleteCurrentItem()" icon="fas fa-trash-alt"> Hapus</x-form.button>
            <x-form.button variant="outline" onclick="closeModal('detailModal')" icon="fas fa-times"> Tutup</x-form.button>
            <x-form.button variant="primary" onclick="editCurrentItem()" icon="fas fa-pen"> Edit</x-form.button>
            <x-form.button variant="success" onclick="restockCurrentItem()" icon="fas fa-redo-alt"> Restock</x-form.button>
        </div>
    </div>
</div>

<!-- ====================== RESTOCK MODAL ====================== -->
<div class="modal-overlay" id="restockModal" onclick="closeModalOut(event,'restockModal')">
    <div class="modal-box" style="max-width:480px">
        <div class="modal-header">
            <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--secondary),var(--success))"><i class="fas fa-redo-alt"></i></div>
            <div class="modal-title"><h3>Restock Barang</h3><p>Tambah stok untuk barang ini</p></div>
            <button class="modal-close" onclick="closeModal('restockModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="restock-current">
                <div class="restock-current-icon" id="rs-icon">📦</div>
                <div>
                    <div class="restock-item-name" id="rs-name">—</div>
                    <div class="restock-item-stock">Stok sekarang: <strong id="rs-stock" class="critical">—</strong></div>
                    <div class="restock-item-stock">Stok minimum: <strong id="rs-min">—</strong></div>
                </div>
            </div>
            <div class="form-grid-2" style="margin-bottom:1rem">
                <x-form.input 
                    formField 
                    label="Jumlah Restock" 
                    type="number"
                    id="rs-qty" 
                    name="qty" 
                    placeholder="0" 
                    min="1"
                    required 
                />

                <x-form.input 
                    formField 
                    label="Satuan" 
                    id="rs-unit" 
                    name="unit" 
                    readonly 
                    style="background:#F9FAFB;color:var(--gray)" 
                />

                <x-form.input 
                    formField 
                    label="Harga Beli/Satuan" 
                    type="number"
                    id="rs-price" 
                    name="price" 
                    placeholder="0" 
                />

                <x-form.input 
                    formField 
                    label="Supplier" 
                    id="rs-supplier" 
                    name="supplier" 
                    placeholder="Nama supplier" 
                />

                <x-form.input 
                    formField 
                    label="Nomor Faktur" 
                    id="rs-invoice" 
                    name="invoice" 
                    placeholder="cth. INV-2024-001" 
                    fullWidth
                />

                <x-form.textarea 
                    label="Catatan" 
                    id="rs-notes" 
                    name="notes" 
                    placeholder="Catatan restock..." 
                    fullWidth 
                />
            </div>
            <div style="padding:.875rem 1rem;background:linear-gradient(135deg,rgba(16,185,129,.05),rgba(6,182,212,.05));border-radius:12px;border:1px solid rgba(16,185,129,.15)">
                <div style="font-size:.8rem;color:var(--gray);margin-bottom:.25rem">Stok setelah restock:</div>
                <div style="font-size:1.25rem;font-weight:800;color:var(--secondary)" id="rs-preview">—</div>
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('restockModal')">Batal</x-form.button>
            <x-form.button variant="success" id="btnConfirmRestock" onclick="confirmRestock()"><i class="fas fa-check btn-icon"></i> <span class="btn-text">Konfirmasi Restock</span></x-form.button>
        </div>
    </div>
</div>

<!-- ====================== AUTO RESTOCK MODAL ====================== -->
<div class="modal-overlay" id="autoRestockModal" onclick="closeModalOut(event,'autoRestockModal')">
    <div class="modal-box" style="max-width:480px">
        <div class="modal-header">
            <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--orange),var(--warning))"><i class="fas fa-redo-alt"></i></div>
            <div class="modal-title"><h3>Restock Otomatis</h3><p>Pilih outlet untuk restock otomatis</p></div>
            <button class="modal-close" onclick="closeModal('autoRestockModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid-2">
                <x-form.select 
                    label="Pilih Outlet" 
                    id="ar-outlet" 
                    name="outlet_id"
                    required
                    fullWidth
                >
                    <option value="">-- Pilih Outlet --</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </x-form.select>

                <x-form.select 
                    label="Filter Kategori (Opsional)" 
                    id="ar-category" 
                    name="category"
                    fullWidth
                >
                    <option value="">Semua Kategori</option>
                    <option>Deterjen & Kimia</option>
                    <option>Pewangi & Softener</option>
                    <option>Plastik & Kemasan</option>
                    <option>Peralatan Cuci</option>
                    <option>Peralatan Setrika</option>
                    <option>Kebersihan Outlet</option>
                    <option>ATK & Administrasi</option>
                </x-form.select>
            </div>
            <p style="font-size: .8rem; color: var(--gray); margin-top: 1rem; line-height: 1.4;">
                * Sistem akan mendeteksi seluruh barang pada outlet terpilih yang jumlah stoknya di bawah batas minimum, lalu otomatis menambahkan stok hingga mencapai batas kapasitas maksimum.
            </p>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('autoRestockModal')">Batal</x-form.button>
            <x-form.button variant="warning" id="btnConfirmAutoRestock" onclick="confirmAutoRestock()"><i class="fas fa-check btn-icon"></i> <span class="btn-text">Mulai Restock</span></x-form.button>
        </div>
    </div>
</div>
