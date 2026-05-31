<div id="globalToastContainer" class="global-toast-container"></div>

<style>
.global-toast-container {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    width: 90%;
    max-width: 420px;
    pointer-events: none;
}

.interactive-toast {
    pointer-events: auto;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 16px;
    padding: 1rem 1.25rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    gap: 0.875rem;
    position: relative;
    overflow: hidden;
    transform: translateY(100px) scale(0.9);
    opacity: 0;
    animation: toastBounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    transition: all 0.3s ease-in-out;
}

@keyframes toastBounceIn {
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

.interactive-toast.hide {
    animation: toastFadeOut 0.3s ease-in-out forwards;
}

@keyframes toastFadeOut {
    to {
        transform: translateY(20px) scale(0.9);
        opacity: 0;
    }
}

.toast-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.toast-success .toast-icon {
    background: rgba(16, 185, 129, 0.15);
    color: #10B981;
}

.toast-error .toast-icon {
    background: rgba(239, 68, 68, 0.15);
    color: #EF4444;
}

.toast-info .toast-icon {
    background: rgba(59, 130, 246, 0.15);
    color: #3B82F6;
}

.toast-body {
    flex-grow: 1;
}

.toast-title {
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 0.15rem;
}

.toast-message {
    font-family: 'Poppins', sans-serif;
    font-size: 0.825rem;
    color: #475569;
    margin: 0;
    line-height: 1.4;
}

.toast-close {
    background: none;
    border: none;
    color: #94A3B8;
    cursor: pointer;
    padding: 0.25rem;
    transition: color 0.2s;
    font-size: 0.95rem;
}

.toast-close:hover {
    color: #475569;
}

.toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: #4F46E5;
    width: 100%;
}

.toast-success .toast-progress {
    background: #10B981;
}

.toast-error .toast-progress {
    background: #EF4444;
}

.toast-info .toast-progress {
    background: #3B82F6;
}
</style>

<script>
window.showToast = function(message, type = 'success', title = null) {
    const container = document.getElementById('globalToastContainer');
    if (!container) return;

    if (!title) {
        title = type === 'success' ? 'Sukses' : (type === 'error' ? 'Error' : 'Informasi');
    }

    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle'
    };

    const toast = document.createElement('div');
    toast.className = `interactive-toast toast-${type}`;
    
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="${icons[type]}"></i>
        </div>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            <p class="toast-message">${message}</p>
        </div>
        <button class="toast-close">
            <i class="fas fa-times"></i>
        </button>
        <div class="toast-progress"></div>
    `;

    container.appendChild(toast);

    // Close button event
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
        closeToast(toast);
    });

    // Animate progress bar
    const progress = toast.querySelector('.toast-progress');
    const duration = 4000; // 4 seconds
    let start = null;

    function step(timestamp) {
        if (!start) start = timestamp;
        const progressTime = timestamp - start;
        const width = 100 - (progressTime / duration) * 100;
        
        if (width >= 0) {
            progress.style.width = width + '%';
            requestAnimationFrame(step);
        } else {
            closeToast(toast);
        }
    }
    
    requestAnimationFrame(step);
}

function closeToast(toast) {
    if (toast.classList.contains('hide')) return;
    toast.classList.add('hide');
    toast.addEventListener('animationend', () => {
        toast.remove();
    });
}
</script>
