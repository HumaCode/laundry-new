<!-- STATUS MODAL -->
<div class="modal-overlay" id="statusModal" onclick="closeModalOutside(event)">
    <div class="modal-box" style="max-width:480px">
        <div class="modal-header">
            <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--secondary),var(--success))"><i class="fas fa-exchange-alt"></i></div>
            <div class="modal-title">
                <h3>Update Status Order</h3>
                <p id="statusModalOrderId">—</p>
            </div>
            <button class="modal-close" onclick="closeModal('statusModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.875rem;color:var(--gray);margin-bottom:1rem">Pilih status baru untuk order ini:</p>
            <div class="status-options">
                <div class="status-option" onclick="selectStatus(this, 'diterima')" data-status="Baru">
                    <div class="status-option-icon"><i class="fas fa-inbox"></i></div>
                    <div class="status-option-label">Diterima</div>
                </div>
                <div class="status-option" onclick="selectStatus(this, 'proses')" data-status="Proses">
                    <div class="status-option-icon"><i class="fas fa-spinner"></i></div>
                    <div class="status-option-label">Diproses</div>
                </div>
                <div class="status-option" onclick="selectStatus(this, 'siap')" data-status="Selesai">
                    <div class="status-option-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="status-option-label">Siap Diambil</div>
                </div>
                <div class="status-option" onclick="selectStatus(this, 'selesai')" data-status="Diambil">
                    <div class="status-option-icon"><i class="fas fa-flag-checkered"></i></div>
                    <div class="status-option-label">Selesai</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <x-form.button variant="outline" onclick="closeModal('statusModal')">Batal</x-form.button>
            <x-form.button variant="success" onclick="saveNewStatus()" icon="fas fa-check"> Konfirmasi</x-form.button>
        </div>
    </div>
</div>
