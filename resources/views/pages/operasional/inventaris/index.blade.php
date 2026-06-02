<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/inventaris.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/inventaris.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Manajemen Inventaris</h2>
            <p>Kelola stok bahan baku, perlengkapan, dan peralatan di seluruh outlet LaundryPro</p>
        </div>
        <div class="page-header-actions">
            <button class="btn-page btn-page-outline" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
            <button class="btn-page btn-page-warning" onclick="openRestockAllModal()"><i class="fas fa-redo-alt"></i> Restock Otomatis</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Barang</button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card c1 active-filter fade-in d1" onclick="filterByStat('all',this)">
            <div class="stat-hdr"><div class="stat-icon c1"><i class="fas fa-boxes"></i></div></div>
            <div class="stat-value" id="sc-all">0</div>
            <div class="stat-label">Total Jenis Barang</div>
            <div class="stat-sub ok" id="sc-all-sub">—</div>
        </div>
        <div class="stat-card c2 fade-in d2" onclick="filterByStat('cukup',this)">
            <div class="stat-hdr"><div class="stat-icon c2"><i class="fas fa-check-circle"></i></div></div>
            <div class="stat-value" id="sc-cukup">0</div>
            <div class="stat-label">Stok Aman</div>
            <div class="stat-sub ok">Di atas batas minimum</div>
        </div>
        <div class="stat-card c3 fade-in d3" onclick="filterByStat('rendah',this)">
            <div class="stat-hdr"><div class="stat-icon c3"><i class="fas fa-exclamation-triangle"></i></div></div>
            <div class="stat-value" id="sc-rendah">0</div>
            <div class="stat-label">Stok Rendah</div>
            <div class="stat-sub warn">Segera restock</div>
        </div>
        <div class="stat-card c4 fade-in d4" onclick="filterByStat('kritis',this)">
            <div class="stat-hdr"><div class="stat-icon c4"><i class="fas fa-fire"></i></div></div>
            <div class="stat-value" id="sc-kritis">0</div>
            <div class="stat-label">Stok Kritis / Habis</div>
            <div class="stat-sub warn">Butuh restock segera!</div>
        </div>
        <div class="stat-card c5 fade-in d5">
            <div class="stat-hdr"><div class="stat-icon c5"><i class="fas fa-dollar-sign"></i></div></div>
            <div class="stat-value" id="sc-nilai">0</div>
            <div class="stat-label">Nilai Inventaris</div>
            <div class="stat-sub ok">Total keseluruhan outlet</div>
        </div>
    </div>

    <!-- Dashboard Grid: Chart + Low Stock -->
    <div class="dash-grid fade-in d3">
        <!-- Stock Usage Bar Chart -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon" style="background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(139,92,246,.12));color:var(--primary)"><i class="fas fa-chart-bar"></i></div>
                    Penggunaan Stok per Kategori
                </div>
                <span style="font-size:.78rem;color:var(--gray);position:relative;z-index:1">Bulan Desember 2024</span>
            </div>
            <div class="card-body">
                <div class="chart-area"><canvas id="stockChart"></canvas></div>
            </div>
        </div>

        <!-- Low / Critical Stock Alert -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon" style="background:rgba(239,68,68,.1);color:var(--danger)"><i class="fas fa-bell"></i></div>
                    Peringatan Stok
                </div>
                <span style="font-size:.78rem;font-weight:600;color:var(--danger);position:relative;z-index:1" id="alertCount">0 item</span>
            </div>
            <div class="card-body" style="max-height:360px;overflow-y:auto" id="alertList"></div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in d4">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari nama barang, kode, merek..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Kategori</label>
            <select class="filter-input" id="filterCat" onchange="applyFilters()">
                <option value="">Semua Kategori</option>
                <option>Deterjen & Kimia</option>
                <option>Pewangi & Softener</option>
                <option>Plastik & Kemasan</option>
                <option>Peralatan Cuci</option>
                <option>Peralatan Setrika</option>
                <option>Kebersihan Outlet</option>
                <option>ATK & Administrasi</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Outlet</label>
            <select class="filter-input" id="filterOutlet" onchange="applyFilters()">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:120px">
            <label class="filter-label">Status Stok</label>
            <select class="filter-input" id="filterStatus" onchange="applyFilters()">
                <option value="">Semua Status</option>
                <option value="lebih">Lebih</option>
                <option value="cukup">Cukup</option>
                <option value="rendah">Rendah</option>
                <option value="kritis">Kritis</option>
                <option value="habis">Habis</option>
            </select>
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- Table -->
    <div class="table-card fade-in d5">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-title-icon"><i class="fas fa-boxes"></i></div>
                Daftar Inventaris
            </div>
            <div class="table-meta">
                <span>Tampil <strong id="showCount">10</strong> dari <strong id="totalCount">0</strong></span>
                <select class="per-page-select" id="perPageSel" onchange="changePerPage(this.value)">
                    <option value="10">10/hal</option><option value="25">25/hal</option><option value="50">50/hal</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th class="cb-cell"><input type="checkbox" class="custom-cb" id="checkAll" onchange="toggleAllCheck()"></th>
                        <th onclick="sortBy('name')">Barang <i class="fas fa-sort sort-icon active" id="si-name"></i></th>
                        <th onclick="sortBy('category')">Kategori <i class="fas fa-sort sort-icon" id="si-category"></i></th>
                        <th onclick="sortBy('outlet')">Outlet <i class="fas fa-sort sort-icon" id="si-outlet"></i></th>
                        <th onclick="sortBy('stock')">Stok <i class="fas fa-sort sort-icon" id="si-stock"></i></th>
                        <th onclick="sortBy('stockStatus')">Status <i class="fas fa-sort sort-icon" id="si-stockStatus"></i></th>
                        <th onclick="sortBy('price')">Harga Satuan <i class="fas fa-sort sort-icon" id="si-price"></i></th>
                        <th onclick="sortBy('value')">Nilai Stok <i class="fas fa-sort sort-icon" id="si-value"></i></th>
                        <th onclick="sortBy('lastRestock')">Restock Terakhir <i class="fas fa-sort sort-icon" id="si-lastRestock"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="invTableBody"></tbody>
            </table>
        </div>
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-boxes"></i></div>
            <div style="font-size:1.125rem;font-weight:700;color:var(--dark);margin-bottom:.5rem">Tidak ada barang ditemukan</div>
            <div style="font-size:.875rem;color:var(--gray)">Coba ubah kata kunci atau filter pencarian</div>
        </div>
        <div class="bulk-bar" id="bulkBar">
            <span class="bulk-count"><i class="fas fa-check-square"></i> <span id="bulkCountText">0</span> dipilih</span>
            <button class="bulk-btn bulk-btn-white" onclick="bulkRestock()"><i class="fas fa-redo-alt"></i> Restock Semua</button>
            <button class="bulk-btn bulk-btn-white" onclick="bulkExport()"><i class="fas fa-file-export"></i> Export</button>
            <button class="bulk-btn bulk-btn-danger" onclick="bulkDelete()"><i class="fas fa-trash-alt"></i> Hapus</button>
            <button class="bulk-close" onclick="clearSelection()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pagination-bar">
            <div class="pagination-info">Halaman <strong id="currentPage">1</strong> dari <strong id="totalPages">1</strong></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

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
                    <div class="form-field"><label>Nama Barang <span class="req">*</span></label><div class="input-wrap"><input class="form-control" id="f-name" type="text" placeholder="cth. Deterjen Attack Cair"><i class="fas fa-tag ic"></i></div></div>
                    <div class="form-field"><label>Kode Barang</label><div class="input-wrap"><input class="form-control" id="f-code" type="text" placeholder="cth. DTJ-001"><i class="fas fa-barcode ic"></i></div></div>
                    <div class="form-field"><label>Kategori <span class="req">*</span></label><select class="form-control" id="f-category"><option>Deterjen & Kimia</option><option>Pewangi & Softener</option><option>Plastik & Kemasan</option><option>Peralatan Cuci</option><option>Peralatan Setrika</option><option>Kebersihan Outlet</option><option>ATK & Administrasi</option></select></div>
                    <div class="form-field"><label>Merek / Supplier</label><div class="input-wrap"><input class="form-control" id="f-brand" type="text" placeholder="cth. Attack / CV Maju Jaya"><i class="fas fa-building ic"></i></div></div>
                    <div class="form-field"><label>Ikon (Emoji)</label>
                        <select class="form-control" id="f-emoji">
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
                        </select>
                    </div>
                    <div class="form-field"><label>Warna Ikon</label><select class="form-control" id="f-color"><option value="#6366F1">Ungu (Deterjen)</option><option value="#10B981">Hijau (Kimia)</option><option value="#F59E0B">Kuning (Peralatan)</option><option value="#EC4899">Pink (Pewangi)</option><option value="#3B82F6">Biru (Kemasan)</option><option value="#F97316">Oranye (ATK)</option></select></div>
                    <div class="form-field full"><label>Deskripsi</label><textarea class="form-control" id="f-desc" placeholder="Deskripsi singkat atau spesifikasi barang..."></textarea></div>
                </div>
                <div class="form-section-title"><i class="fas fa-layer-group"></i> Stok & Harga</div>
                <div class="form-grid-3">
                    <div class="form-field"><label>Stok Awal <span class="req">*</span></label><input class="form-control" id="f-stock" type="number" placeholder="0" min="0"></div>
                    <div class="form-field"><label>Stok Minimum</label><input class="form-control" id="f-minStock" type="number" placeholder="0" min="0"></div>
                    <div class="form-field"><label>Stok Maksimum</label><input class="form-control" id="f-maxStock" type="number" placeholder="0" min="0"></div>
                    <div class="form-field"><label>Satuan</label><select class="form-control" id="f-unit"><option>liter</option><option>kg</option><option>pcs</option><option>botol</option><option>dus</option><option>pack</option><option>roll</option><option>lembar</option></select></div>
                    <div class="form-field"><label>Harga Beli/Satuan</label><input class="form-control" id="f-price" type="number" placeholder="0" min="0"></div>
                    <div class="form-field"><label>Outlet <span class="req">*</span></label><select class="form-control" id="f-outlet">
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('itemModal')"><i class="fas fa-times"></i> Batal</button>
                <button class="modal-btn modal-btn-primary" id="btnSaveItem" onclick="saveItem()"><i class="fas fa-save btn-icon"></i> <span class="btn-text">Simpan</span></button>
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
                        <div class="detail-field"><label>Deskripsi</label><div class="val" id="dm-desc" style="grid-column:span 2;font-weight:400;color:var(--gray)">—</div></div>
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
                <button class="modal-btn modal-btn-danger"  onclick="deleteCurrentItem()"><i class="fas fa-trash-alt"></i> Hapus</button>
                <button class="modal-btn modal-btn-outline" onclick="closeModal('detailModal')"><i class="fas fa-times"></i> Tutup</button>
                <button class="modal-btn modal-btn-primary" onclick="editCurrentItem()"><i class="fas fa-pen"></i> Edit</button>
                <button class="modal-btn modal-btn-success" onclick="restockCurrentItem()"><i class="fas fa-redo-alt"></i> Restock</button>
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
                    <div class="form-field"><label>Jumlah Restock <span class="req">*</span></label><input class="form-control" id="rs-qty" type="number" placeholder="0" min="1"></div>
                    <div class="form-field"><label>Satuan</label><input class="form-control" id="rs-unit" type="text" readonly style="background:#F9FAFB;color:var(--gray)"></div>
                    <div class="form-field"><label>Harga Beli/Satuan</label><input class="form-control" id="rs-price" type="number" placeholder="0"></div>
                    <div class="form-field"><label>Supplier</label><input class="form-control" id="rs-supplier" type="text" placeholder="Nama supplier"></div>
                    <div class="form-field full"><label>Nomor Faktur</label><input class="form-control" id="rs-invoice" type="text" placeholder="cth. INV-2024-001"></div>
                    <div class="form-field full"><label>Catatan</label><textarea class="form-control" id="rs-notes" placeholder="Catatan restock..." style="min-height:60px"></textarea></div>
                </div>
                <div style="padding:.875rem 1rem;background:linear-gradient(135deg,rgba(16,185,129,.05),rgba(6,182,212,.05));border-radius:12px;border:1px solid rgba(16,185,129,.15)">
                    <div style="font-size:.8rem;color:var(--gray);margin-bottom:.25rem">Stok setelah restock:</div>
                    <div style="font-size:1.25rem;font-weight:800;color:var(--secondary)" id="rs-preview">—</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('restockModal')">Batal</button>
                <button class="modal-btn modal-btn-success" id="btnConfirmRestock" onclick="confirmRestock()"><i class="fas fa-check btn-icon"></i> <span class="btn-text">Konfirmasi Restock</span></button>
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
                    <div class="form-field full">
                        <label>Pilih Outlet <span class="req">*</span></label>
                        <select class="form-control" id="ar-outlet">
                            <option value="">-- Pilih Outlet --</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field full">
                        <label>Filter Kategori (Opsional)</label>
                        <select class="form-control" id="ar-category">
                            <option value="">Semua Kategori</option>
                            <option>Deterjen & Kimia</option>
                            <option>Pewangi & Softener</option>
                            <option>Plastik & Kemasan</option>
                            <option>Peralatan Cuci</option>
                            <option>Peralatan Setrika</option>
                            <option>Kebersihan Outlet</option>
                            <option>ATK & Administrasi</option>
                        </select>
                    </div>
                </div>
                <p style="font-size: .8rem; color: var(--gray); margin-top: 1rem; line-height: 1.4;">
                    * Sistem akan mendeteksi seluruh barang pada outlet terpilih yang jumlah stoknya di bawah batas minimum, lalu otomatis menambahkan stok hingga mencapai batas kapasitas maksimum.
                </p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('autoRestockModal')">Batal</button>
                <button class="modal-btn modal-btn-warning" id="btnConfirmAutoRestock" onclick="confirmAutoRestock()"><i class="fas fa-check btn-icon"></i> <span class="btn-text">Mulai Restock</span></button>
            </div>
        </div>
    </div>

    <!-- Toast + Float btn -->
    <div class="toast-wrap" id="toastWrap"></div>
    <div class="float-btn-container">
        <button class="float-btn" id="scrollTopBtn" onclick="window.scrollToTop(event)">
            <div class="float-btn-ring"></div>
            <i class="fas fa-arrow-up"></i>
            <span class="float-btn-tooltip">Kembali ke Atas</span>
        </button>
    </div>
</x-app-layout>
