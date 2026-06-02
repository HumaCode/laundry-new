<!-- TAMBAH / EDIT LAYANAN MODAL -->
<div class="modal-overlay" id="serviceModal" onclick="closeModalOutside(event, 'serviceModal')">
    <div class="modal-box" style="max-width: 750px;">
        <!-- Modal Header -->
        <div class="modal-header">
            <div class="modal-header-icon" id="modalIconEl" style="background: linear-gradient(135deg, var(--primary), var(--purple)); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fas fa-concierge-bell"></i>
            </div>
            <div class="modal-title">
                <h3 id="modalTitleEl" style="margin: 0; font-size: 1.125rem; font-weight: 700;">Tambah Layanan</h3>
                <p id="modalSubEl" style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--gray);">Isi detail layanan baru</p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('serviceModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="serviceForm" onsubmit="event.preventDefault(); saveService();">
            <!-- Modal Body -->
            <div class="modal-body">
                
                <!-- Section 1: Basic Info -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle" style="color: var(--primary)"></i> Informasi Layanan
                    </div>
                    
                    <!-- Row 1: Nama Layanan -->
                    <x-form.input 
                        formField 
                        label="Nama Layanan" 
                        id="f-name" 
                        name="name" 
                        placeholder="cth. Cuci Setrika" 
                        icon="fas fa-tag" 
                        required 
                    />

                    <!-- Row 2: Emoji Selection Grid (Inline, no dropdown overflow) -->
                    <div class="form-field" style="margin-bottom: 1.25rem;">
                        <label>Ikon (Emoji) <span class="req">*</span></label>
                        <div class="emoji-selector-grid" style="display: grid; grid-template-columns: repeat(9, 1fr); gap: 0.5rem; background: #F9FAFB; border: 2px solid var(--border); border-radius: 16px; padding: 0.75rem;">
                            <!-- Row 1 -->
                            <button type="button" class="emoji-btn-item active" onclick="selectEmoji('🧺', this)">🧺</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👕', this)">👕</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👖', this)">👖</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧼', this)">🧼</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('💦', this)">💦</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👟', this)">👟</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧣', this)">🧣</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧦', this)">🧦</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧥', this)">🧥</button>
                            
                            <!-- Row 2 -->
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👗', this)">👗</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👔', this)">👔</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧸', this)">🧸</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🛏️', this)">🛏️</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('⚡', this)">⚡</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🛵', this)">🛵</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🌸', this)">🌸</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👜', this)">👜</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('✨', this)">✨</button>

                            <!-- Row 3 -->
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧢', this)">🧢</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧤', this)">🧤</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👘', this)">👘</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👙', this)">👙</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🥼', this)">🥼</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🎒', this)">🎒</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧳', this)">🧳</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧵', this)">🧵</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🪡', this)">🪡</button>

                            <!-- Row 4 -->
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧶', this)">🧶</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧹', this)">🧹</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧴', this)">🧴</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🧽', this)">🧽</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🪣', this)">🪣</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('🫧', this)">🫧</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👞', this)">👞</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('👠', this)">👠</button>
                            <button type="button" class="emoji-btn-item" onclick="selectEmoji('☂️', this)">☂️</button>
                        </div>
                        <input type="hidden" id="f-emoji" value="🧺">
                    </div>

                    <!-- Row 3: Kategori & Estimasi -->
                    <div class="form-grid-2">
                        <x-form.select 
                            label="Kategori" 
                            id="f-category" 
                            name="category"
                            required
                        >
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                            <option value="paket">Paket</option>
                            <option value="antar">Antar Jemput</option>
                        </x-form.select>

                        <x-form.input 
                            formField 
                            label="Estimasi Waktu" 
                            id="f-eta" 
                            name="eta" 
                            placeholder="cth. 1-2 hari" 
                            icon="fas fa-clock" 
                        />
                    </div>
                    
                    <!-- Row 4: Deskripsi -->
                    <x-form.textarea 
                        label="Deskripsi" 
                        id="f-desc" 
                        name="description" 
                        placeholder="Deskripsi singkat layanan ini..." 
                        rows="3" 
                        fullWidth 
                    />
                </div>

                <!-- Section 2: Pricing -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-money-bill-wave" style="color: var(--secondary)"></i> Pengaturan Harga
                    </div>
                    <div class="form-grid-2" style="margin-bottom: 1rem;">
                        <x-form.input 
                            formField 
                            label="Harga Dasar" 
                            type="number"
                            id="f-price" 
                            name="price" 
                            placeholder="0" 
                            min="0"
                            icon="fas fa-money-bill-wave" 
                            required 
                        />

                        <x-form.select 
                            label="Satuan Harga" 
                            id="f-unit" 
                            name="unit"
                            required
                        >
                            <option value="/kg">per kg</option>
                            <option value="/pcs">per pcs</option>
                            <option value="/set">per set</option>
                            <option value="/pair">per pair</option>
                            <option value="/m²">per m²</option>
                            <option value="/trip">per trip</option>
                            <option value="/bulan">per bulan</option>
                        </x-form.select>
                    </div>
                    
                    <label style="font-size: 0.75rem; margin-bottom: 0.6rem; color: var(--gray); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block;">Tingkatan Harga (Opsional)</label>
                    <div class="price-tiers-editor" id="priceTiersEditor" style="display: flex; flex-direction: column; gap: 0.5rem;"></div>
                    <button type="button" class="btn-add-tier" onclick="addPriceTier()" style="margin-top: 0.75rem;"><i class="fas fa-plus"></i> Tambah Tingkatan Harga</button>
                </div>

                <!-- Section 3: Target & Info -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-chart-bar" style="color: var(--orange)"></i> Target & Kustomisasi
                    </div>
                    <div class="form-grid-3">
                        <x-form.input 
                            formField 
                            label="Target Order/Bulan" 
                            type="number"
                            id="f-target" 
                            name="target" 
                            placeholder="cth. 200" 
                        />

                        <x-form.input 
                            formField 
                            label="Min. Berat/Qty" 
                            id="f-min" 
                            name="min_qty" 
                            placeholder="cth. 1 kg" 
                        />

                        <x-form.select 
                            label="Warna Kartu" 
                            id="f-color" 
                            name="color"
                        >
                            <option value="sc-purple">Ungu</option>
                            <option value="sc-green">Hijau</option>
                            <option value="sc-orange">Oranye</option>
                            <option value="sc-pink">Pink</option>
                            <option value="sc-blue">Biru</option>
                            <option value="sc-teal">Teal</option>
                            <option value="sc-red">Merah</option>
                            <option value="sc-indigo">Indigo</option>
                        </x-form.select>
                    </div>
                </div>

                <!-- Section 4: Features -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-check-circle" style="color: var(--secondary)"></i> Fitur Layanan
                    </div>
                    <div id="featuresEditor" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;"></div>
                    <button type="button" class="btn-add-tier" onclick="addFeatureRow()"><i class="fas fa-plus"></i> Tambah Fitur</button>
                </div>

                <!-- Section 5: Settings -->
                <div class="form-section" style="margin-bottom: 1rem;">
                    <div class="form-section-title">
                        <i class="fas fa-sliders-h" style="color: var(--gray)"></i> Pengaturan
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <h4>Status Aktif</h4>
                                <p>Tampilkan layanan ini kepada pelanggan</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="f-aktif" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <h4>Express Tersedia</h4>
                                <p>Layanan ini bisa diproses express (harga x1.5)</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="f-express">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <h4>Antar Jemput</h4>
                                <p>Tersedia layanan antar/jemput untuk ini</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="f-pickup">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <x-form.button type="button" variant="outline" onclick="closeModal('serviceModal')">Batal</x-form.button>
                <x-form.button type="submit" variant="primary" id="btnSaveService">
                    <div class="spinner"></div>
                    <span class="btn-text"><i class="fas fa-save" style="margin-right: 0.35rem;"></i> Simpan Layanan</span>
                </x-form.button>
            </div>
        </form>
    </div>
</div>
