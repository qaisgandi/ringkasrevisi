<!-- Modals Portal (Fixed overlays) -->
<!-- Modal: Tentang -->
<div id="about-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] scale-95 opacity-0 transition-all duration-300 transform" id="about-modal-content">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-bps-navy text-white">
            <div class="flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-bps-orange"></i>
                <h3 class="heading-font font-bold text-base">Tentang Portal RINGKAS</h3>
            </div>
            <button type="button" onclick="closeFooterModal('about-modal')" class="p-1 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4 text-slate-600 text-sm leading-relaxed">
            <p>
                <strong>RINGKAS</strong> (Sistem Ringkasan Publikasi Statistik BPS) adalah inisiatif digital inovatif yang dikembangkan untuk menjembatani kompleksitas publikasi Badan Pusat Statistik dengan kebutuhan pembaca umum, akademisi, dan pengambil keputusan.
            </p>
            <p>
                Setiap tahunnya, BPS menerbitkan ribuan buku publikasi yang berisi jutaan data tabel. Melalui teknologi kecerdasan buatan (AI) terintegrasi, RINGKAS mengekstrak dan menyusun intisari poin-poin utama dari publikasi tebal tersebut ke dalam format ringkasan ringkas, terstruktur, dan mudah dibaca dalam waktu singkat.
            </p>
            <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-100/50 space-y-2">
                <h4 class="font-bold text-bps-navy text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="award" class="w-4 h-4 text-bps-lightBlue"></i> Nilai Utama Kami
                </h4>
                <ul class="list-disc pl-4 text-xs space-y-1.5 font-medium text-slate-600">
                    <li><strong>Efisien</strong>: Menghemat waktu pembacaan publikasi dari berjam-jam menjadi hitungan menit.</li>
                    <li><strong>Akurat</strong>: Menjaga substansi dan validitas indikator statistik resmi BPS.</li>
                    <li><strong>Aksesibel</strong>: Memudahkan siapapun memahami statistik dasar sektoral maupun wilayah.</li>
                </ul>
            </div>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closeFooterModal('about-modal')" class="px-5 py-2 bg-bps-navy hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">
                Tutup Halaman
            </button>
        </div>
    </div>
</div>

<!-- Modal: Panduan -->
<div id="guide-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] scale-95 opacity-0 transition-all duration-300 transform" id="guide-modal-content">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-bps-navy text-white">
            <div class="flex items-center gap-2">
                <i data-lucide="book-open" class="w-5 h-5 text-bps-orange"></i>
                <h3 class="heading-font font-bold text-base">Panduan Penggunaan Portal</h3>
            </div>
            <button type="button" onclick="closeFooterModal('guide-modal')" class="p-1 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4 text-slate-600 text-sm">
            <p class="leading-relaxed font-medium">Berikut adalah langkah-langkah mudah untuk memanfaatkan fitur sistem ringkasan publikasi:</p>
            <div class="space-y-4">
                <!-- Step 1 -->
                <div class="flex gap-3.5">
                    <div class="w-7 h-7 rounded-xl bg-blue-50 text-bps-lightBlue flex items-center justify-center font-bold text-xs shrink-0 shadow-sm border border-blue-100/30">1</div>
                    <div class="space-y-0.5">
                        <h4 class="font-bold text-bps-navy text-xs">Pendaftaran Akun &amp; Login</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Silakan melakukan registrasi menggunakan data Anda untuk mendapatkan hak akses ke dalam dasbor portal ringkasan.</p>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="flex gap-3.5">
                    <div class="w-7 h-7 rounded-xl bg-blue-50 text-bps-lightBlue flex items-center justify-center font-bold text-xs shrink-0 shadow-sm border border-blue-100/30">2</div>
                    <div class="space-y-0.5">
                        <h4 class="font-bold text-bps-navy text-xs">Pencarian Publikasi</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Pada halaman beranda, gunakan kolom pencarian atau filter kategori untuk mencari judul publikasi resmi BPS yang Anda butuhkan.</p>
                    </div>
                </div>
                <!-- Step 3 -->
                <div class="flex gap-3.5">
                    <div class="w-7 h-7 rounded-xl bg-blue-50 text-bps-lightBlue flex items-center justify-center font-bold text-xs shrink-0 shadow-sm border border-blue-100/30">3</div>
                    <div class="space-y-0.5">
                        <h4 class="font-bold text-bps-navy text-xs">Melihat Hasil Ringkasan AI</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Pilih salah satu publikasi, lalu masuk ke menu ringkasan untuk melihat intisari data secara to-the-point yang dipilah berdasarkan isu strategis.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closeFooterModal('guide-modal')" class="px-5 py-2 bg-bps-navy hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">
                Tutup Halaman
            </button>
        </div>
    </div>
</div>

<!-- Modal: FAQ -->
<div id="faq-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] scale-95 opacity-0 transition-all duration-300 transform" id="faq-modal-content">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-bps-navy text-white">
            <div class="flex items-center gap-2">
                <i data-lucide="help-circle" class="w-5 h-5 text-bps-orange"></i>
                <h3 class="heading-font font-bold text-base">Tanya Jawab (FAQ)</h3>
            </div>
            <button type="button" onclick="closeFooterModal('faq-modal')" class="p-1 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4 text-slate-600 text-sm">
            <div class="space-y-3.5">
                <div>
                    <h4 class="font-bold text-bps-navy text-xs">Q: Apakah ringkasan AI di sini sudah pasti akurat?</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">A: Ringkasan dibuat secara otomatis dari dokumen PDF resmi BPS. Kami berupaya menjaga ketelitian ekstraksi data. Namun, pengguna disarankan tetap merujuk pada publikasi asli jika ingin melakukan kutipan resmi.</p>
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <h4 class="font-bold text-bps-navy text-xs">Q: Bagaimana cara mendownload ringkasan?</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">A: Anda dapat melihat dan menyalin teks ringkasan langsung dari dasbor Anda. Fitur ekspor ke format PDF ringkas akan segera hadir pada pengembangan selanjutnya.</p>
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <h4 class="font-bold text-bps-navy text-xs">Q: Apakah saya dapat mengajukan publikasi baru untuk diringkas?</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">A: Tentu. Hubungi administrator melalui email layanan agar kami dapat memproses dan mengunggah publikasi yang Anda butuhkan ke sistem.</p>
                </div>
            </div>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closeFooterModal('faq-modal')" class="px-5 py-2 bg-bps-navy hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">
                Tutup Halaman
            </button>
        </div>
    </div>
</div>

<!-- Modal: Kontak -->
<div id="contact-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] scale-95 opacity-0 transition-all duration-300 transform" id="contact-modal-content">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-bps-navy text-white">
            <div class="flex items-center gap-2">
                <i data-lucide="contact" class="w-5 h-5 text-bps-orange"></i>
                <h3 class="heading-font font-bold text-base">Detail Kontak BPS Kota Tasikmalaya</h3>
            </div>
            <button type="button" onclick="closeFooterModal('contact-modal')" class="p-1 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4 text-slate-600 text-sm">
            <p class="leading-relaxed font-medium">Layanan Statistik Terpadu BPS Kota Tasikmalaya siap melayani Anda melalui:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Telepon Resmi</span>
                    <p class="text-xs font-bold text-slate-800 leading-tight">+62 265 346022</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pengaduan (WA)</span>
                    <p class="text-xs font-bold text-slate-800 leading-tight">0851-1717-3278</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Surel Resmi</span>
                    <p class="text-xs font-bold text-slate-800 leading-tight">bps3278@bps.go.id</p>
                    <p class="text-xs font-semibold text-slate-500 leading-tight">Subjek: Layanan RINGKAS</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 col-span-1 sm:col-span-2 space-y-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alamat Kantor</span>
                    <p class="text-xs font-bold text-slate-800 leading-relaxed">Jl. Sukarindik No. 71, Tasikmalaya 46151, Jawa Barat</p>
                </div>
            </div>
            <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100 flex gap-3 text-xs text-amber-800">
                <i data-lucide="info" class="w-5 h-5 shrink-0 mt-0.5 text-bps-orange"></i>
                <div class="space-y-0.5 leading-relaxed">
                    <strong class="font-bold">Informasi Jam Layanan PST:</strong>
                    <p class="font-medium text-amber-700">Layanan offline di Kantor BPS Kota Tasikmalaya buka pada hari kerja Senin-Jumat pukul 08.00 s.d. 15.30 WIB.</p>
                </div>
            </div>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closeFooterModal('contact-modal')" class="px-5 py-2 bg-bps-navy hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">
                Tutup Halaman
            </button>
        </div>
    </div>
</div>

<script>
    // Handle modal opens
    function openFooterModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + '-content');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Small timeout to allow transition to trigger
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                if (content) {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }
            }, 10);
            document.body.classList.add('overflow-hidden');
        }
    }

    // Handle modal closes
    function closeFooterModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + '-content');
        if (modal) {
            if (content) {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Close modals on clicking backdrop
    document.addEventListener('DOMContentLoaded', () => {
        ['about-modal', 'guide-modal', 'faq-modal', 'contact-modal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeFooterModal(modalId);
                    }
                });
            }
        });
    });
</script>
