// ============================================
// SIDEBAR TOGGLE
// ============================================
const sidebar = document.getElementById('sidebar');
const topbar = document.getElementById('topbar');
const mainContent = document.getElementById('mainContent');
let isCollapsed = false;

function toggleSidebar() {
    isCollapsed = !isCollapsed;
    if (sidebar) sidebar.classList.toggle('collapsed', isCollapsed);
    if (topbar) topbar.classList.toggle('sidebar-collapsed', isCollapsed);
    if (mainContent) mainContent.classList.toggle('sidebar-collapsed', isCollapsed);
}

function toggleMobileSidebar(event) {
    if (event) event.stopPropagation();
    if (sidebar) sidebar.classList.toggle('mobile-open');
}

function confirmLogout(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Hide the dropdown menu if it was open
    const dropdownMenu = document.getElementById('topbarUserDropdown');
    if (dropdownMenu) {
        dropdownMenu.classList.remove('show');
    }
    
    const logoutModal = document.getElementById('logoutModal');
    if (logoutModal) {
        logoutModal.classList.add('show');
    }
}

function closeLogoutModal() {
    const logoutModal = document.getElementById('logoutModal');
    if (logoutModal) {
        logoutModal.classList.remove('show');
    }
}

function executeLogout() {
    const confirmBtn = document.getElementById('btnConfirmLogout');
    const cancelBtn = document.querySelector('.modal-btn-cancel');
    if (!confirmBtn) return;
    
    // Add loading class, disable buttons, and change text
    confirmBtn.classList.add('loading');
    confirmBtn.disabled = true;
    if (cancelBtn) cancelBtn.disabled = true;
    
    const btnText = confirmBtn.querySelector('.btn-text');
    if (btnText) btnText.textContent = 'Sedang proses...';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    $.ajax({
        url: '/logout',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function() {
            if (typeof window.showToast === 'function') {
                window.showToast('Logout berhasil! Mengalihkan...', 'success', 'Keluar');
            }
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);
        },
        error: function(xhr) {
            console.error('Logout error:', xhr);
            // Revert state
            confirmBtn.classList.remove('loading');
            confirmBtn.disabled = false;
            if (cancelBtn) cancelBtn.disabled = false;
            if (btnText) btnText.textContent = 'Ya, Logout';
            
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal melakukan logout. Silakan coba lagi.', 'error', 'Error');
            } else {
                alert('Gagal melakukan logout. Silakan coba lagi.');
            }
        }
    });
}

function handleLogout(event) {
    confirmLogout(event);
}

// Mobile sidebar click handler
document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768) {
        if (!e.target.closest('#sidebar') && !e.target.closest('.sidebar-toggle')) {
            if (sidebar) sidebar.classList.remove('mobile-open');
        }
    }
});

// ============================================
// FLOAT BUTTON — SCROLL TO TOP SHOW/HIDE
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            scrollTopBtn.classList.toggle('visible', window.scrollY > 300);
        });
    }

    // Dropdown toggle logic
    const dropdownToggle = document.getElementById('topbarUserDropdownToggle');
    const dropdownMenu = document.getElementById('topbarUserDropdown');
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        document.addEventListener('click', (e) => {
            if (!dropdownToggle.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }

    // Submenu toggle logic
    document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('nav-submenu')) {
                submenu.classList.toggle('show');
                const caret = this.querySelector('.submenu-caret');
                if (caret) {
                    if (submenu.classList.contains('show')) {
                        caret.style.transform = 'rotate(180deg)';
                    } else {
                        caret.style.transform = 'rotate(0deg)';
                    }
                }
            }
        });
    });
});

// Scroll to Top Smooth Animation with Bounce
function scrollToTop(event) {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (!scrollTopBtn) return;

    // Ripple effect
    const ripple = document.createElement('span');
    ripple.classList.add('ripple');
    ripple.style.width = ripple.style.height = '44px';
    ripple.style.left = ripple.style.top = '0px';
    scrollTopBtn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);

    // Bounce scroll
    const start = window.scrollY;
    const duration = 800;
    const startTime = performance.now();

    function easeOutBounce(t) {
        const n1 = 7.5625, d1 = 2.75;
        if (t < 1/d1) return n1*t*t;
        else if (t < 2/d1) return n1*(t -= 1.5/d1)*t + 0.75;
        else if (t < 2.5/d1) return n1*(t -= 2.25/d1)*t + 0.9375;
        else return n1*(t -= 2.625/d1)*t + 0.984375;
    }

    function step(now) {
        const p = Math.min((now - startTime) / duration, 1);
        window.scrollTo(0, start * (1 - easeOutBounce(p)));
        if (p < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}

// Expose functions globally to be called from blade onclick attributes
window.toggleSidebar = toggleSidebar;
window.toggleMobileSidebar = toggleMobileSidebar;
window.handleLogout = handleLogout;
window.scrollToTop = scrollToTop;
window.confirmLogout = confirmLogout;
window.closeLogoutModal = closeLogoutModal;
window.executeLogout = executeLogout;
