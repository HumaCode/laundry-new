<div id="globalLoadingOverlay" class="global-loading-overlay" style="display: none;">
    <div class="loading-card-content">
        <div class="laundry-spinner">
            <div class="outer-ring"></div>
            <div class="inner-drum">
                <i class="fas fa-soap bubble-1"></i>
                <i class="fas fa-soap bubble-2"></i>
                <i class="fas fa-soap bubble-3"></i>
            </div>
        </div>
        <p class="loading-text">Mohon Tunggu...</p>
    </div>
</div>

<style>
.global-loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease-in-out;
}

.loading-card-content {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border-radius: 24px;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    max-width: 320px;
    width: 90%;
    transform: scale(0.9);
    animation: loadingScaleIn 0.3s forwards cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes loadingScaleIn {
    to { transform: scale(1); }
}

.laundry-spinner {
    position: relative;
    width: 80px;
    height: 80px;
}

.outer-ring {
    position: absolute;
    inset: 0;
    border: 4px solid #E2E8F0;
    border-top-color: #4F46E5;
    border-bottom-color: #EC4899;
    border-radius: 50%;
    animation: loadingSpin 1.2s infinite linear;
}

.inner-drum {
    position: absolute;
    inset: 12px;
    background: linear-gradient(135deg, #EEF2F6 0%, #E2E8F0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.inner-drum::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 80%);
}

.bubble-1, .bubble-2, .bubble-3 {
    position: absolute;
    color: #38BDF8;
    opacity: 0.7;
    font-size: 0.75rem;
}

.bubble-1 {
    top: 20%;
    left: 25%;
    animation: floatBubble 2s infinite ease-in-out;
}

.bubble-2 {
    top: 50%;
    right: 20%;
    animation: floatBubble 2.5s infinite ease-in-out 0.5s;
}

.bubble-3 {
    bottom: 25%;
    left: 45%;
    animation: floatBubble 1.8s infinite ease-in-out 0.2s;
}

@keyframes loadingSpin {
    to { transform: rotate(360deg); }
}

@keyframes floatBubble {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.4; }
    50% { transform: translateY(-8px) scale(1.2); opacity: 0.8; }
}

.loading-text {
    font-family: 'Outfit', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #0F172A;
    margin: 0;
    animation: pulseText 1.5s infinite alternate ease-in-out;
}

@keyframes pulseText {
    from { opacity: 0.6; }
    to { opacity: 1; }
}
</style>

<script>
window.showLoadingDialog = function() {
    const overlay = document.getElementById('globalLoadingOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
    }
}

window.hideLoadingDialog = function() {
    const overlay = document.getElementById('globalLoadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}
</script>
