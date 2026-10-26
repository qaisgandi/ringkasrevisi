import DOMPurify from 'dompurify';
import { upload as uploadToVercelBlob } from '@vercel/blob/client';
window.DOMPurify = DOMPurify;

// Consolidated script for BPS Ringkasan application
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-publication-upload]').forEach(form => {
        const input = form.querySelector('input[type="file"][name="pdf"]');
        const zone = form.querySelector('[data-drop-zone]');
        const feedback = form.querySelector('#file-feedback');
        const error = form.querySelector('[data-file-error]');
        if (!input) return;

        const validateFile = file => {
            if (!file) return '';
            if (!/\.pdf$/i.test(file.name) || (file.type && file.type !== 'application/pdf')) {
                return 'Pilih dokumen berformat PDF.';
            }
            if (file.size > 25 * 1024 * 1024) return 'Ukuran PDF maksimal 25 MB.';
            return '';
        };

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            const message = validateFile(file);
            input.setCustomValidity(message);
            if (error) {
                error.textContent = message;
                error.hidden = !message;
            }
            if (feedback) feedback.textContent = message || (file ? `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB` : 'Belum ada berkas dipilih.');
        });

        if (zone) {
            zone.addEventListener('dragover', event => {
                event.preventDefault();
                zone.classList.add('is-dragging');
            });
            ['dragleave', 'drop'].forEach(type => zone.addEventListener(type, event => {
                event.preventDefault();
                zone.classList.remove('is-dragging');
            }));
            zone.addEventListener('drop', event => {
                const file = event.dataTransfer?.files?.[0];
                if (!file) return;
                const transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        form.addEventListener('submit', async event => {
            const file = input.files?.[0];
            if (form.dataset.blobUploadUrl && file && form.dataset.blobUploadComplete !== 'true') {
                event.preventDefault();
                const submitButton = form.querySelector('button[type="submit"]');
                const progress = form.querySelector('[data-upload-progress]');
                const progressBar = progress?.querySelector('progress');
                const status = form.querySelector('[data-upload-status]');
                const error = form.querySelector('[data-file-error]');
                const blobUrlInput = form.querySelector('[name="pdf_blob_url"]');
                const fileSizeInput = form.querySelector('[name="uploaded_file_size"]');
                const csrfToken = form.querySelector('input[name="_token"]')?.value;

                if (error) { error.hidden = true; error.textContent = ''; }
                if (submitButton) { submitButton.disabled = true; submitButton.dataset.uploading = 'true'; }
                if (progress) progress.hidden = false;
                if (status) status.textContent = 'Mengirim dokumen langsung ke penyimpanan aman…';

                try {
                    const tokenResponse = await fetch(form.dataset.uploadTokenUrl, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: '{}',
                    });
                    const tokenData = await tokenResponse.json();
                    if (!tokenResponse.ok || !tokenData.token) throw new Error(tokenData.message || 'Otorisasi unggah gagal. Muat ulang halaman dan coba lagi.');

                    const blob = await uploadToVercelBlob(`publications/${crypto.randomUUID()}.pdf`, file, {
                        access: 'public',
                        contentType: 'application/pdf',
                        handleUploadUrl: form.dataset.blobUploadUrl,
                        clientPayload: tokenData.token,
                        onUploadProgress: ({ percentage }) => {
                            if (progressBar) progressBar.value = percentage;
                            if (status) status.textContent = `Mengunggah dokumen… ${Math.round(percentage)}%`;
                        },
                    });

                    blobUrlInput.value = blob.url;
                    fileSizeInput.value = String(file.size);
                    form.dataset.blobUploadComplete = 'true';
                    input.disabled = true;
                    if (status) status.textContent = 'Dokumen tersimpan. Menyimpan data publikasi…';
                    form.requestSubmit(submitButton || undefined);
                } catch (uploadError) {
                    console.error('Publication upload failed:', uploadError);
                    if (error) {
                        error.textContent = uploadError.message || 'Unggahan gagal. Periksa koneksi lalu coba kembali.';
                        error.hidden = false;
                    }
                    if (progress) progress.hidden = true;
                    if (submitButton) { submitButton.disabled = false; delete submitButton.dataset.uploading; }
                }
                return;
            }

            const message = validateFile(input.files?.[0]);
            input.setCustomValidity(message);
            if (message) {
                event.preventDefault();
                input.reportValidity();
                return;
            }
            const progress = form.querySelector('[data-upload-progress]');
            if (progress) progress.hidden = false;
        });
    });


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

    document.querySelectorAll('form[data-submit-state]').forEach(form => {
        form.addEventListener('submit', event => {
            if (event.defaultPrevented || !form.checkValidity()) return;
            const button = form.querySelector('button[type="submit"]');
            if (!button) return;
            button.dataset.originalLabel = button.textContent;
            button.disabled = true;
            button.textContent = 'Memproses…';
            form.setAttribute('aria-busy', 'true');
        });
    });

    window.addEventListener('pageshow', () => {
        document.querySelectorAll('button[data-original-label]').forEach(button => {
            button.textContent = button.dataset.originalLabel;
            button.disabled = false;
            button.closest('form')?.removeAttribute('aria-busy');
        });
    });
});
