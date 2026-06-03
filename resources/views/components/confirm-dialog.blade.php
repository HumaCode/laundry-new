<div id="dynamicConfirmOverlay" class="modal-overlay-custom" onclick="closeConfirmOutside(event)">
    <div id="dynamicConfirmBox" class="modal-container-custom">
        <!-- Pulse Wave Icon -->
        <div class="pulse-wave-wrapper" id="confirmPulseWrapper">
            <div class="pulse-wave-ring" style="background: rgba(239, 68, 68, 0.12)"></div>
            <div class="pulse-wave-ring" style="background: rgba(239, 68, 68, 0.12)"></div>
            <div class="pulse-wave-ring" style="background: rgba(239, 68, 68, 0.12)"></div>
            <div class="pulse-wave-icon" id="confirmPulseIcon" style="background: linear-gradient(135deg, var(--danger), #DC2626); box-shadow: 0 8px 20px rgba(239, 68, 68, 0.35);">
                <i class="fas fa-exclamation-triangle" id="confirmFaIcon"></i>
            </div>
        </div>
        
        <!-- Content -->
        <h3 id="confirmTitle" class="modal-title-custom">Konfirmasi Tindakan</h3>
        <p id="confirmMessage" class="modal-desc-custom">Apakah Anda yakin ingin melakukan tindakan ini?</p>
        
        <!-- Actions -->
        <div class="modal-btn-group">
            <button type="button" class="modal-btn-custom modal-btn-cancel" onclick="closeConfirmDialog()">Batal</button>
            <button type="button" class="modal-btn-custom modal-btn-confirm" id="confirmYesBtn">
                <i class="fas fa-trash-alt"></i> Hapus
            </button>
        </div>
    </div>
</div>

<script>
window.showConfirm = function(title, message, onConfirm, options = {}) {
    const overlay = document.getElementById('dynamicConfirmOverlay');
    const titleEl = document.getElementById('confirmTitle');
    const msgEl = document.getElementById('confirmMessage');
    const confirmBtn = document.getElementById('confirmYesBtn');
    
    // Pulse wave custom styling elements
    const pulseRings = document.querySelectorAll('#confirmPulseWrapper .pulse-wave-ring');
    const pulseIcon = document.getElementById('confirmPulseIcon');
    const faIcon = document.getElementById('confirmFaIcon');
    
    if (!overlay) return;
    
    titleEl.textContent = title;
    msgEl.textContent = message;
    
    // Clear and clone confirm button to remove previous event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Apply options defaults
    const confirmText = options.confirmText || 'Hapus';
    const confirmIcon = options.confirmIcon || 'fa-trash-alt';
    const confirmBg = options.confirmBg || 'linear-gradient(135deg, var(--danger), #DC2626)';
    const confirmShadow = options.confirmShadow || '0 4px 15px rgba(239, 68, 68, 0.3)';
    
    // Pulse Icon configuration (defaults to danger/red)
    const iconClass = options.icon || 'fa-exclamation-triangle';
    const pulseBg = options.pulseBg || 'linear-gradient(135deg, var(--danger), #DC2626)';
    const pulseShadow = options.pulseShadow || '0 8px 20px rgba(239, 68, 68, 0.35)';
    const ringColor = options.ringColor || 'rgba(239, 68, 68, 0.12)';
    
    // Apply pulse icon configs
    if (faIcon) {
        faIcon.className = `fas ${iconClass}`;
    }
    if (pulseIcon) {
        pulseIcon.style.background = pulseBg;
        pulseIcon.style.boxShadow = pulseShadow;
    }
    pulseRings.forEach(ring => {
        ring.style.background = ringColor;
    });
    
    // Set confirm button icon, text, and styles
    newConfirmBtn.innerHTML = `<i class="fas ${confirmIcon}"></i> <span class="btn-text">${confirmText}</span>`;
    newConfirmBtn.style.background = confirmBg;
    newConfirmBtn.style.boxShadow = confirmShadow;
    
    overlay.classList.add('show');
    
    newConfirmBtn.addEventListener('click', function() {
        const originalHtml = newConfirmBtn.innerHTML;
        newConfirmBtn.disabled = true;
        newConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang proses...';
        
        // Execute dynamic callback
        Promise.resolve(onConfirm()).then(() => {
            overlay.classList.remove('show');
        }).catch((err) => {
            console.error(err);
        }).finally(() => {
            newConfirmBtn.disabled = false;
            newConfirmBtn.innerHTML = originalHtml;
        });
    });
};

window.closeConfirmDialog = function() {
    const overlay = document.getElementById('dynamicConfirmOverlay');
    if (overlay) overlay.classList.remove('show');
};

window.closeConfirmOutside = function(e) {
    if (e.target === e.currentTarget) {
        closeConfirmDialog();
    }
};
</script>
