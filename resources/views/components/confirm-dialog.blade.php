<div id="dynamicConfirmOverlay" class="confirm-overlay" onclick="closeConfirmOutside(event)">
    <div id="dynamicConfirmBox" class="confirm-island">
        <!-- Island Icon/Header -->
        <div class="confirm-island-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <!-- Content -->
        <div class="confirm-island-content">
            <h4 id="confirmTitle" class="confirm-island-title">Konfirmasi Tindakan</h4>
            <p id="confirmMessage" class="confirm-island-message">Apakah Anda yakin ingin melakukan tindakan ini?</p>
        </div>
        
        <!-- Actions -->
        <div class="confirm-island-actions">
            <button type="button" class="confirm-island-btn btn-cancel" onclick="closeConfirmDialog()">Batal</button>
            <button type="button" class="confirm-island-btn btn-confirm" id="confirmYesBtn">
                <i class="fas fa-trash-alt"></i> Hapus
            </button>
        </div>
    </div>
</div>

<style>
.confirm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45); /* Slate 900 tint */
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 99999;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding-top: 5rem;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.35s ease, visibility 0.35s ease;
}

.confirm-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Dynamic Island modal container starting as small status pill */
.confirm-island {
    background: #1E293B; /* Slate 800 premium dark theme */
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    width: 380px;
    max-width: 90%;
    border-radius: 40px;
    padding: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 30px rgba(99, 102, 241, 0.15);
    text-align: center;
    
    /* Dynamic Island spring scale transition */
    transform: translateY(-80px) scaleX(0.2) scaleY(0.08);
    opacity: 0;
    transition: transform 0.65s cubic-bezier(0.19, 1, 0.22, 1), 
                opacity 0.45s ease,
                border-radius 0.5s ease;
}

.confirm-overlay.show .confirm-island {
    transform: translateY(0) scaleX(1) scaleY(1);
    opacity: 1;
    border-radius: 28px;
}

/* Icon */
.confirm-island-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(236, 72, 153, 0.2));
    color: #F43F5E;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1.125rem;
    animation: pulseConfirmGlow 2s infinite;
}

@keyframes pulseConfirmGlow {
    0%, 100% { box-shadow: 0 0 10px rgba(239, 68, 68, 0.2); }
    50% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.5); }
}

.confirm-island-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.15rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    color: white;
    letter-spacing: -0.5px;
}

.confirm-island-message {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.85rem;
    color: #94A3B8;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}

.confirm-island-actions {
    display: flex;
    gap: 0.75rem;
}

.confirm-island-btn {
    flex: 1;
    padding: 0.75rem;
    border-radius: 16px;
    font-size: 0.875rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.confirm-island-btn.btn-cancel {
    background: rgba(255, 255, 255, 0.08);
    color: #CBD5E1;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.confirm-island-btn.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.15);
    color: white;
}

.confirm-island-btn.btn-confirm {
    background: linear-gradient(135deg, #EF4444, #EC4899);
    color: white;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.confirm-island-btn.btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
}

.confirm-island-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}
</style>

<script>
window.showConfirm = function(title, message, onConfirm) {
    const overlay = document.getElementById('dynamicConfirmOverlay');
    const titleEl = document.getElementById('confirmTitle');
    const msgEl = document.getElementById('confirmMessage');
    const confirmBtn = document.getElementById('confirmYesBtn');
    
    if (!overlay) return;
    
    titleEl.textContent = title;
    msgEl.textContent = message;
    
    // Clear and clone confirm button to remove previous event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
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
