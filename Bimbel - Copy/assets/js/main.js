// Bimbel Portal - Main JavaScript
// Contains common utility functions and event handlers

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips or other components as needed
    console.log('Bimbel Portal initialized');
    initMobileSidebar();
});

/**
 * Mobile Sidebar Navigation Handler
 */
function initMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;

    if (window._sidebarInitialized) return;
    window._sidebarInitialized = true;

    // 1. Ensure overlay exists
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.setAttribute('aria-hidden', 'true');
        document.body.appendChild(overlay);
    }

    // 2. Ensure close button exists in sidebar-brand
    const brand = sidebar.querySelector('.sidebar-brand');
    if (brand && !brand.querySelector('.sidebar-close-btn')) {
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'sidebar-close-btn';
        closeBtn.setAttribute('aria-label', 'Tutup Menu');
        closeBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        `;
        brand.appendChild(closeBtn);
    }

    // 3. Ensure toggle button exists in all topbars/headers
    const topbars = document.querySelectorAll('.topbar, .header, .dashboard-header');
    topbars.forEach(topbar => {
        if (!topbar.querySelector('.sidebar-toggle-btn')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.className = 'sidebar-toggle-btn';
            toggleBtn.setAttribute('aria-label', 'Buka Menu Sidebar');
            toggleBtn.innerHTML = `
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            `;
            topbar.insertAdjacentElement('afterbegin', toggleBtn);
        }
    });

    // 4. Centralized click event delegation - prevents double firing and supports SVGs
    document.addEventListener('click', function(e) {
        // Check toggle button
        const toggleBtn = e.target.closest('.sidebar-toggle-btn, #sidebarToggle, #mobileSidebarToggle');
        if (toggleBtn) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
            return;
        }

        // Check close button
        const closeBtn = e.target.closest('.sidebar-close-btn');
        if (closeBtn) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
            return;
        }

        // Check overlay click
        if (e.target.matches('.sidebar-overlay') || e.target.closest('.sidebar-overlay')) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
            return;
        }
    });

    function toggleSidebar() {
        const isOpen = document.body.classList.contains('sidebar-open') || 
                       document.querySelector('.sidebar.active') !== null;
        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    function openSidebar() {
        document.querySelectorAll('.sidebar').forEach(el => el.classList.add('active'));
        document.body.classList.add('sidebar-open');
        const currentOverlay = document.querySelector('.sidebar-overlay');
        if (currentOverlay) currentOverlay.classList.add('active');
    }

    function closeSidebar() {
        document.querySelectorAll('.sidebar').forEach(el => el.classList.remove('active'));
        document.body.classList.remove('sidebar-open');
        const currentOverlay = document.querySelector('.sidebar-overlay');
        if (currentOverlay) currentOverlay.classList.remove('active');
    }

    // Close when Escape key pressed
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });

    // Auto-close on mobile when a nav item link is clicked
    document.addEventListener('click', function(e) {
        const navLink = e.target.closest('.sidebar .sidebar-nav a, .sidebar .nav-item, .sidebar .nav-sub-item');
        if (navLink && window.innerWidth <= 768) {
            closeSidebar();
        }
    });

    // Touch swipe left on sidebar to close on mobile
    let touchStartX = 0;
    let touchStartY = 0;
    sidebar.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });

    sidebar.addEventListener('touchend', function(e) {
        const touchEndX = e.changedTouches[0].screenX;
        const touchEndY = e.changedTouches[0].screenY;
        if (touchStartX - touchEndX > 45 && Math.abs(touchStartY - touchEndY) < 80) {
            closeSidebar();
        }
    }, { passive: true });

    // Handle resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && document.body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });
}

/**
 * Format number to currency (Rupiah)
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

/**
 * Format date to Indonesian format
 */
function formatDate(date) {
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
}

/**
 * Show notification/alert
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone number format
 */
function isValidPhone(phone) {
    const re = /^(\+62|62|0)[0-9]{9,12}$/;
    return re.test(phone.replace(/[^0-9+]/g, ''));
}
