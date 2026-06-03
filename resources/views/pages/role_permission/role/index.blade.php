<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/role-permission.css'])
    @endpush

    @push('scripts')
        <script>
            // Bootstrap initial state from backend database
            window.INITIAL_ROLES = @json($roles);
            window.INITIAL_MENUS = @json($menus);
            window.INITIAL_USERS = @json($users);
            window.INITIAL_ROLE_USERS = @json($roleUsers);
        </script>
        @vite(['resources/js/admin/role-permission.js'])
    @endpush

    <!-- BREADCRUMB -->
    <div class="breadcrumb-custom mb-4 animate-fade-up d1">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item-custom">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
        <span class="breadcrumb-item-custom active">Role & Permission</span>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="roles-layout">
        <!-- LEFT PANEL: ROLE CARDS & USERS LIST -->
        <div>
            <!-- Header -->
            <div class="roles-panel-header animate-fade-up d1">
                <div class="roles-panel-title">
                    <div class="roles-panel-title-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <span>Daftar Role</span>
                </div>
                @can('create roles')
                <button class="btn-add-role" onclick="openAddRoleModal()">
                    <i class="fas fa-plus"></i> Tambah
                </button>
                @else
                <button class="btn-add-role" onclick="openAddRoleModal()">
                    <i class="fas fa-plus"></i> Tambah
                </button>
                @endcan
            </div>

            <!-- Role List -->
            <div id="roleList" class="animate-fade-up d2">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-circle-notch fa-spin fa-2x mb-2"></i>
                    <div>Memuat daftar role...</div>
                </div>
            </div>

            <!-- Users in Role Panel -->
            <div class="users-panel animate-fade-up d3" id="usersPanel" style="display: none;">
                <div class="users-panel-header">
                    <div class="users-panel-title">
                        <i class="fas fa-users-cog text-primary"></i>
                        <span>Pengguna dengan Role <span id="ur-role-name" class="text-primary font-weight-bold"></span></span>
                    </div>
                    <button class="btn-assign" onclick="openAssignModal()">
                        <i class="fas fa-user-plus"></i> Tambah
                    </button>
                </div>
                <div id="usersList">
                    <!-- Users list will render here -->
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: PERMISSION MATRIX -->
        <div class="perm-panel animate-fade-up d2">
            <!-- Header -->
            <div class="perm-panel-header">
                <div class="perm-panel-header-top">
                    <div class="selected-role-info">
                        <div class="selected-role-icon" id="sr-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <div class="selected-role-name" id="sr-name">Pilih Role</div>
                            <div class="selected-role-sub" id="sr-desc">Klik role di sebelah kiri untuk melihat permission</div>
                        </div>
                    </div>
                    <div class="perm-header-actions">
                        <button class="perm-action-btn pab-reset" onclick="resetPerms()">
                            <i class="fas fa-undo"></i> Batal
                        </button>
                        <button class="perm-action-btn pab-save" onclick="savePerms()">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </div>

                <!-- Quick toggles -->
                <div class="perm-quick-toggles">
                    <button class="quick-toggle qt-all-on" onclick="toggleAllPerms(true)">
                        <i class="fas fa-check-double"></i> Aktifkan Semua
                    </button>
                    <button class="quick-toggle qt-all-off" onclick="toggleAllPerms(false)">
                        <i class="fas fa-ban"></i> Matikan Semua
                    </button>
                    <button class="quick-toggle qt-readonly" onclick="setReadOnly()">
                        <i class="fas fa-eye"></i> View Only
                    </button>
                </div>
            </div>

            <!-- Permission list -->
            <div class="perm-body" id="permBody">
                <div style="padding: 3rem; text-align: center; color: var(--gray-light)">
                    <div style="font-size: 3rem; margin-bottom: 1rem; opacity: .25">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div style="font-size: 1rem; font-weight: 600; color: var(--gray); margin-bottom: .5rem">Pilih Role Terlebih Dahulu</div>
                    <div style="font-size: .875rem">Klik salah satu role di panel kiri untuk mengatur permission</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FLOAT ACTION BUTTONS (SCROLL TO TOP) -->
    <div class="float-btn-container">
        <button class="float-btn" id="scrollTopBtn" onclick="scrollToTop(event)">
            <div class="float-btn-ring"></div>
            <i class="fas fa-chevron-up"></i>
            <span class="float-btn-tooltip">Kembali ke Atas</span>
        </button>
    </div>

    <!-- MODAL 1: ADD & EDIT ROLE -->
    <div class="modal-overlay" id="roleModal" onclick="closeModalOut(event, 'roleModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" id="rm-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="modal-title">
                    <h3 id="rm-title">Buat Role Baru</h3>
                    <p id="rm-sub">Tentukan nama, ikon, dan warna role</p>
                </div>
                <button class="modal-close" onclick="closeModal('roleModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-section-title"><i class="fas fa-info-circle"></i> Informasi Utama</div>
                
                <div class="form-grid-2">
                    <div class="form-field">
                        <label for="rm-name">Nama Role <span class="req">*</span></label>
                        <input type="text" id="rm-name" class="form-control" placeholder="Contoh: Manager Operasional" required>
                    </div>
                    <div class="form-field">
                        <label for="rm-slug">Slug Role</label>
                        <input type="text" id="rm-slug" class="form-control" placeholder="Otomatis dari nama" readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                    </div>
                </div>

                <div class="form-field full mb-4">
                    <label for="rm-desc">Deskripsi</label>
                    <textarea id="rm-desc" class="form-control" placeholder="Jelaskan ruang lingkup dan tanggung jawab role ini..."></textarea>
                </div>

                <div class="form-section-title"><i class="fas fa-palette"></i> Tampilan & Prioritas</div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label for="rm-level">Level Prioritas</label>
                        <input type="number" id="rm-level" class="form-control" min="1" max="100" value="2">
                    </div>
                    <div class="form-field">
                        <label>Warna Representatif</label>
                        <div class="color-options" id="colorOptions">
                            <!-- Color circles render here -->
                        </div>
                    </div>
                </div>

                <div class="form-field full mt-3">
                    <label>Ikon Role</label>
                    <div class="icon-options mt-2" id="iconOptions">
                        <!-- Icon selections render here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('roleModal')">Batal</button>
                <button class="modal-btn modal-btn-primary" onclick="saveRole()"><i class="fas fa-save"></i> Simpan Role</button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: ASSIGN USERS -->
    <div class="modal-overlay" id="assignModal" onclick="closeModalOut(event, 'assignModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:linear-gradient(135deg, var(--primary), var(--purple))">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="modal-title">
                    <h3>Tambah Pengguna</h3>
                    <p id="am-role-name">Tambahkan pengguna baru ke role ini</p>
                </div>
                <button class="modal-close" onclick="closeModal('assignModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding-bottom: 0;">
                <div class="form-field full mb-3">
                    <label for="userSearchInput">Cari Pengguna</label>
                    <div style="position:relative">
                        <input type="text" id="userSearchInput" class="form-control" placeholder="Ketik nama atau email..." oninput="filterAssignUsers(this.value)">
                        <i class="fas fa-search" style="position:absolute;right:16px;top:16px;color:var(--gray-light)"></i>
                    </div>
                </div>
                <div class="form-section-title"><i class="fas fa-list"></i> Hasil Pencarian</div>
            </div>
            <div class="modal-body" style="padding-top: 0; max-height: 280px;">
                <div class="user-search-results" id="userSearchResults">
                    <!-- Search item options render here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('assignModal')">Batal</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmAssign()"><i class="fas fa-check"></i> Tambahkan Terpilih</button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: COPY PERMISSIONS -->
    <div class="modal-overlay" id="copyModal" onclick="closeModalOut(event, 'copyModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:linear-gradient(135deg, var(--primary), var(--purple))">
                    <i class="fas fa-copy"></i>
                </div>
                <div class="modal-title">
                    <h3>Salin Permission</h3>
                    <p>Duplikasi semua aturan permission dari role lain</p>
                </div>
                <button class="modal-close" onclick="closeModal('copyModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding-bottom: 0;">
                <div class="form-field full mb-3">
                    <label>Pilih Role Sumber</label>
                    <p style="font-size:0.75rem; color:var(--gray-light)">Semua permission dari role yang dipilih akan disalin ke role saat ini.</p>
                </div>
            </div>
            <div class="modal-body" style="padding-top: 0; max-height: 280px;">
                <div id="copyRoleOptions">
                    <!-- Copy role item options render here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('copyModal')">Batal</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmCopy()"><i class="fas fa-check"></i> Salin Sekarang</button>
            </div>
        </div>
    </div>
</x-app-layout>
