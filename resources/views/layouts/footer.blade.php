<footer class="bg-bps-navy text-slate-300 border-t border-slate-800">
    <!-- Top Footer: Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-12 md:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12">
            
            <!-- Column 1: Brand Info (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-1.5 rounded-xl shadow-md shadow-black/10 inline-block">
                        <img src="{{ asset('images/logo-bps.svg') }}" alt="Logo BPS" class="w-10 h-8 object-contain">
                    </div>
                    <div>
                        <h4 class="heading-font text-base font-black text-white leading-none tracking-wider">RINGKAS</h4>
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">SISTEM RINGKASAN PUBLIKASI STATISTIK BPS</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed font-medium">
                    Portal digital bertenaga AI yang merangkum ribuan halaman publikasi statistik Badan Pusat Statistik menjadi informasi ringkas yang mudah dipahami demi mendukung literasi data nasional.
                </p>
                <div class="space-y-4 pt-1">
                    <!-- BPS Link -->
                    <div>
                        <a href="https://www.bps.go.id" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600/35 border border-blue-500/30 hover:border-blue-500/50 rounded-xl text-xs font-semibold text-bps-lightBlue transition-all" title="Kunjungi Situs Resmi BPS">
                            <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                            Portal Resmi BPS
                        </a>
                    </div>
                    <!-- Copyright Notice -->
                    <p class="text-[11px] font-medium text-slate-500">
                        &copy; {{ date('Y') }} Badan Pusat Statistik. Hak Cipta Dilindungi.
                    </p>
                </div>
            </div>

            <!-- Column 2: Tentang (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <h5 class="heading-font text-xs font-black text-white uppercase tracking-wider">Tentang</h5>
                <ul class="space-y-2.5 text-xs font-medium text-slate-400">
                    <li>
                        <button type="button" onclick="openFooterModal('about-modal')" class="hover:text-bps-lightBlue hover:pl-1 transition-all text-left focus:outline-none">
                            Tentang RINGKAS
                        </button>
                    </li>
                    <li>
                        <a href="https://www.bps.go.id/id/term-of-service" target="_blank" rel="noopener noreferrer" class="hover:text-bps-lightBlue hover:pl-1 transition-all block text-left">
                            Ketentuan Layanan
                        </a>
                    </li>
                    <li>
                        <a href="https://www.bps.go.id/id/privacy-policy" target="_blank" rel="noopener noreferrer" class="hover:text-bps-lightBlue hover:pl-1 transition-all block text-left">
                            Kebijakan Privasi
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Panduan (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <h5 class="heading-font text-xs font-black text-white uppercase tracking-wider">Panduan</h5>
                <ul class="space-y-2.5 text-xs font-medium text-slate-400">
                    <li>
                        <button type="button" onclick="openFooterModal('guide-modal')" class="hover:text-bps-lightBlue hover:pl-1 transition-all text-left focus:outline-none">
                            Cara Penggunaan
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="openFooterModal('faq-modal')" class="hover:text-bps-lightBlue hover:pl-1 transition-all text-left focus:outline-none">
                            Tanya Jawab (FAQ)
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Column 4: Kontak (3 cols) -->
            <div class="lg:col-span-3 space-y-4">
                <h5 class="heading-font text-xs font-black text-white uppercase tracking-wider">Kontak BPS Kota Tasikmalaya</h5>
                <ul class="space-y-2.5 text-xs font-medium text-slate-400">
                    <li class="flex items-start gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-bps-orange shrink-0 mt-0.5"></i>
                        <span>Jl. Sukarindik No. 71, Tasikmalaya 46151, Jawa Barat</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="mail" class="w-4 h-4 text-bps-orange shrink-0"></i>
                        <a href="mailto:bps3278@bps.go.id" class="hover:text-bps-lightBlue transition-colors">bps3278@bps.go.id</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="phone" class="w-4 h-4 text-bps-orange shrink-0"></i>
                        <a href="tel:+62265346022" class="hover:text-bps-lightBlue transition-colors">+62 265 346022</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-bps-orange shrink-0"></i>
                        <span>Pengaduan: <a href="tel:085117173278" class="hover:text-bps-lightBlue transition-colors">0851-1717-3278</a></span>
                    </li>
                    <li>
                        <button type="button" onclick="openFooterModal('contact-modal')" class="text-bps-lightBlue hover:underline text-left font-bold focus:outline-none flex items-center gap-1 mt-1">
                            Form Detail Kontak <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </li>
                </ul>
            </div>

        </div>
    </div></footer>

@include('layouts.footer_modals')
