import DOMPurify from 'dompurify';
window.DOMPurify = DOMPurify;
import { createIcons, icons } from 'lucide';
window.lucide = { createIcons: () => createIcons({ icons }) };
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
            this.setAttribute('aria-pressed', String(isPassword));
            this.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            if (!this.querySelector('svg, i')) this.textContent = isPassword ? 'Sembunyikan' : 'Tampilkan';

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
            sidebarToggle?.setAttribute('aria-expanded', String(!sidebar.classList.contains('-translate-x-full')));
            if (!sidebar.classList.contains('-translate-x-full')) sidebarClose?.focus(); else sidebarToggle?.focus();
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
            profileCard.setAttribute('aria-expanded', String(!profileDropdown.classList.contains('hidden')));
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

// Progressive enhancements for shared navigation and native form submission.
document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('[data-nav-toggle]');
    const menu = document.getElementById('public-nav');
    const closeMenu = () => { menu?.classList.remove('is-open'); menuButton?.setAttribute('aria-expanded', 'false'); };
    menuButton?.addEventListener('click', () => {
        const open = menu.classList.toggle('is-open');
        menuButton.setAttribute('aria-expanded', String(open));
    });
    menu?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
    document.querySelectorAll('form[data-submit-state]').forEach(form => {
        form.addEventListener('submit', event => {
            if (event.defaultPrevented || !form.checkValidity()) return;
            const button = form.querySelector('button[type="submit"]');
            if (button) { button.dataset.originalLabel = button.textContent; button.disabled = true; button.textContent = 'Memproses…'; form.setAttribute('aria-busy', 'true'); }
        });
    });
    window.addEventListener('pageshow', () => document.querySelectorAll('[data-original-label]').forEach(button => {
        button.textContent = button.dataset.originalLabel; button.disabled = false; button.closest('form')?.removeAttribute('aria-busy');
    }));
    // Existing icon-only buttons already have titles; expose them to assistive technology.
    document.querySelectorAll('button[title], a[title]').forEach(el => { if (!el.textContent.trim() && !el.hasAttribute('aria-label')) el.setAttribute('aria-label', el.title); });
    document.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(el => {
        if (!el.labels?.length && !el.hasAttribute('aria-label')) el.setAttribute('aria-label', el.placeholder || el.name || 'Isian formulir');
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-publication-upload]');
    if (!form) return;
    const input = form.querySelector('[name="pdf"]');
    const zone = form.querySelector('[data-drop-zone]');
    const error = form.querySelector('[data-file-error]');
    const feedback = form.querySelector('#file-feedback');
    const validate = () => {
        const file = input.files[0];
        const message = file && !/\.pdf$/i.test(file.name) ? 'Pilih berkas dengan format PDF.' : file && file.size > 25 * 1024 * 1024 ? 'Ukuran PDF melebihi batas 25 MB.' : '';
        error.textContent = message; error.hidden = !message;
        input.setCustomValidity(message);
        input.setAttribute('aria-invalid', String(Boolean(message)));
        feedback.textContent = file ? `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB` : 'Belum ada berkas dipilih.';
        return !message;
    };
    input.addEventListener('change', validate);
    ['dragenter', 'dragover'].forEach(event => zone.addEventListener(event, e => { e.preventDefault(); zone.classList.add('is-dragging'); }));
    ['dragleave', 'drop'].forEach(event => zone.addEventListener(event, e => { e.preventDefault(); zone.classList.remove('is-dragging'); }));
    zone.addEventListener('drop', e => {
        if (e.dataTransfer.files.length !== 1) { error.textContent = 'Pilih satu dokumen PDF untuk setiap publikasi.'; error.hidden = false; return; }
        input.files = e.dataTransfer.files; validate();
    });
    form.addEventListener('submit', e => {
        if (!validate() || !form.checkValidity()) { e.preventDefault(); form.reportValidity(); return; }
        const button = form.querySelector('button[type="submit"]');
        button.dataset.originalLabel = button.textContent; button.disabled = true; button.textContent = 'Memproses…';
        form.setAttribute('aria-busy', 'true'); form.querySelector('[data-upload-progress]').hidden = false;
    });
});

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('#categoryChart, #monthlyChart, #visitsDailyChart, #visitsRegionChart')) {
        import('chart.js/auto').then(({default: Chart}) => {
            window.Chart = Chart;
            document.dispatchEvent(new Event('ringkas:charts-ready'));
        }).catch(() => {
            document.querySelectorAll('.rk-chart-data').forEach(detail => detail.open = true);
        });
    }
    const profile = document.getElementById('profile-card');
    profile?.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); profile.click(); } });
    document.querySelectorAll('#delete-modal, #clear-modal').forEach(modal => {
        modal.setAttribute('role', 'dialog'); modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-label', modal.querySelector('h3')?.textContent || 'Konfirmasi');
        modal.querySelector('button')?.setAttribute('aria-label', 'Tutup dialog');
        modal.addEventListener('keydown', e => {
            if (e.key === 'Escape') { if (modal.id === 'delete-modal') window.closeDeleteModal?.(); else window.closeClearModal?.(); }
            if (e.key === 'Tab') {
                const items = [...modal.querySelectorAll('button, a[href], input')].filter(el => !el.disabled && el.getClientRects().length);
                const first = items[0], last = items[items.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last?.focus(); }
                if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first?.focus(); }
            }
        });
    });
});
