// Consolidated script for BPS Ringkasan application
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // 1. PASSWORD VISIBILITY TOGGLE (GLOBAL)
    // ==========================================
    // Supports element selector .toggle-password-btn or #toggle-password
    const toggleButtons = document.querySelectorAll('.toggle-password-btn, #toggle-password');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Check if we use data-target attribute, otherwise default to "password"
            const targetId = this.getAttribute('data-target') || 'password';
            const passwordInput = document.getElementById(targetId);
            if (!passwordInput) return;

            const isPassword = passwordInput.getAttribute('type') === 'password';
            const type = isPassword ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Locate the eye icons: either inside class list or by ID
            const eyeOpen = this.querySelector('.eye-open') || document.getElementById('eye-icon-open');
            const eyeClosed = this.querySelector('.eye-closed') || document.getElementById('eye-icon-closed');

            if (eyeOpen && eyeClosed) {
                if (type === 'password') {
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                    this.setAttribute('title', 'Tampilkan Password');
                } else {
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                    this.setAttribute('title', 'Sembunyikan Password');
                }
            }
        });
    });

    // ==========================================
    // 2. ADMIN PANEL SIDEBAR & DROPDOWNS
    // ==========================================
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
        if (sidebar && sidebarOverlay) {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

    // Notification Dropdown Toggle
    const bellBtn = document.getElementById('notification-bell-btn');
    const dropdown = document.getElementById('notification-dropdown');

    if (bellBtn && dropdown) {
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }

    // Profile Card Dropdown Toggle
    const profileCard = document.getElementById('profile-card');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileCard && profileDropdown) {
        profileCard.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileCard.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    }

    // ==========================================
    // 3. LUCIDE ICONS AUTO-INITIALIZATION
    // ==========================================
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
