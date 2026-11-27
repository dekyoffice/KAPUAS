<!-- Sliding Side-Drawer: Cascading Program Picker -->
<div id="drawer-program-picker-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
    <div id="drawer-program-picker" class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div class="w-screen max-w-md sm:max-w-lg md:max-w-xl bg-white shadow-2xl flex flex-col transform transition-transform duration-300 ease-in-out translate-x-full">
            
            <!-- Drawer Header -->
            <div class="px-6 py-5 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-white leading-tight">Pilih Hierarki Program Kerja</h3>
                        <p class="text-[11px] text-slate-400">Pilih secara berurutan: Tahun → Program → Kegiatan → Sub Kegiatan</p>
                    </div>
                </div>

                <button type="button" onclick="closeProgramPickerDrawer()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Breadcrumbs Bar -->
            <div class="px-6 py-3 bg-slate-50 border-b border-slate-200">
                <div class="flex items-center gap-1.5 text-xs overflow-x-auto py-1 scrollbar-none" id="drawer-breadcrumbs">
                    <!-- Dynamic Breadcrumbs rendered here -->
                </div>
            </div>

            <!-- Drawer Body & Step Navigator -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4">

                <!-- Header Step & Back Button -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span id="drawer-step-badge" class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                            1
                        </span>
                        <h4 id="drawer-step-title" class="text-sm font-bold text-slate-800">
                            Pilih Tahun Penetapan
                        </h4>
                    </div>

                    <button type="button" id="drawer-back-btn" onclick="drawerGoBack()" class="hidden text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali</span>
                    </button>
                </div>

                <!-- Search Input for filtering list items -->
                <div class="relative" id="drawer-search-container">
                    <input type="text" id="drawer-search-input" onkeyup="filterDrawerItems()" placeholder="Cari dalam daftar..."
                        class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-slate-800">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Loading State -->
                <div id="drawer-loading" class="hidden py-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-600 border-t-transparent"></div>
                    <p class="text-xs font-semibold text-slate-500 mt-2">Memuat data hierarki...</p>
                </div>

                <!-- Step Content Container -->
                <div id="drawer-items-list" class="space-y-2.5">
                    <!-- Cards will be populated here via JavaScript -->
                </div>

                <!-- Empty State -->
                <div id="drawer-empty-state" class="hidden py-12 text-center">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-700" id="drawer-empty-title">Data Tidak Ditemukan</p>
                    <p class="text-[11px] text-slate-500 mt-0.5" id="drawer-empty-desc">Tidak ada item yang sesuai dengan kriteria yang dipilih.</p>
                </div>

            </div>

            <!-- Drawer Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-[11px] text-slate-500">
                    Langkah <span id="drawer-step-indicator" class="font-bold text-slate-800">1 dari 4</span>
                </span>
                <button type="button" onclick="closeProgramPickerDrawer()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors">
                    Batal
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    // State Drawer
    const drawerState = {
        currentStep: 1, // 1: Tahun, 2: Program, 3: Kegiatan, 4: SubKegiatan
        selectedTahun: null,
        selectedProgram: null,
        selectedKegiatan: null,
        selectedSubKegiatan: null,
        dataTahun: [],
        dataPrograms: [],
        dataKegiatans: [],
        dataSubKegiatans: [],
    };

    // DOM Elements
    const backdropEl = document.getElementById('drawer-program-picker-backdrop');
    const drawerEl = document.getElementById('drawer-program-picker').querySelector('.transform');
    const loadingEl = document.getElementById('drawer-loading');
    const listEl = document.getElementById('drawer-items-list');
    const emptyEl = document.getElementById('drawer-empty-state');
    const searchInput = document.getElementById('drawer-search-input');
    const stepBadge = document.getElementById('drawer-step-badge');
    const stepTitle = document.getElementById('drawer-step-title');
    const stepIndicator = document.getElementById('drawer-step-indicator');
    const backBtn = document.getElementById('drawer-back-btn');
    const breadcrumbsEl = document.getElementById('drawer-breadcrumbs');

    // Helper: Escape HTML string to prevent XSS / formatting corruption
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showDrawerError(message = "Gagal memuat data. Muat ulang halaman atau hubungi administrator.") {
        loadingEl.classList.add('hidden');
        listEl.innerHTML = '';
        const titleEl = document.getElementById('drawer-empty-title');
        const descEl = document.getElementById('drawer-empty-desc');
        if (titleEl) titleEl.textContent = 'Terjadi Kesalahan';
        if (descEl) descEl.textContent = message;
        emptyEl.classList.remove('hidden');
    }

    function showDrawerEmpty(title = "Data Tidak Ditemukan", desc = "Tidak ada item yang sesuai dengan kriteria yang dipilih.") {
        loadingEl.classList.add('hidden');
        const titleEl = document.getElementById('drawer-empty-title');
        const descEl = document.getElementById('drawer-empty-desc');
        if (titleEl) titleEl.textContent = title;
        if (descEl) descEl.textContent = desc;
        emptyEl.classList.remove('hidden');
    }

    // Open Drawer Function
    function openProgramPickerDrawer() {
        backdropEl.classList.remove('pointer-events-none', 'opacity-0');
        backdropEl.classList.add('opacity-100');
        drawerEl.classList.remove('translate-x-full');
        drawerEl.classList.add('translate-x-0');

        // Reset to Step 1 or maintain selected year
        if (!drawerState.selectedTahun) {
            loadStepTahun();
        } else {
            renderCurrentStep();
        }
    }

    // Close Drawer Function
    function closeProgramPickerDrawer() {
        backdropEl.classList.remove('opacity-100');
        backdropEl.classList.add('opacity-0', 'pointer-events-none');
        drawerEl.classList.remove('translate-x-0');
        drawerEl.classList.add('translate-x-full');
    }

    // Backdrop click listener to close
    backdropEl.addEventListener('click', function(e) {
        if (e.target === backdropEl || e.target.id === 'drawer-program-picker') {
            closeProgramPickerDrawer();
        }
    });

    // Escape key listener to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !backdropEl.classList.contains('pointer-events-none')) {
            closeProgramPickerDrawer();
        }
    });

    // Render Breadcrumbs
    function updateBreadcrumbs() {
        let html = `
            <button type="button" onclick="goToStep(1)" class="font-semibold text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-1 whitespace-nowrap">
                <span>Pilih Tahun</span>
            </button>
        `;

        if (drawerState.selectedTahun) {
            html += `
                <span class="text-slate-400">/</span>
                <button type="button" onclick="goToStep(2)" class="font-semibold ${drawerState.currentStep === 2 ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600'} transition-colors whitespace-nowrap">
                    Tahun ${escapeHtml(drawerState.selectedTahun)}
                </button>
            `;
        }

        if (drawerState.selectedProgram) {
            const progName = escapeHtml(drawerState.selectedProgram.nama_program);
            html += `
                <span class="text-slate-400">/</span>
                <button type="button" onclick="goToStep(3)" class="font-semibold ${drawerState.currentStep === 3 ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600'} transition-colors max-w-[120px] truncate" title="${progName}">
                    ${progName}
                </button>
            `;
        }

        if (drawerState.selectedKegiatan) {
            const kegName = escapeHtml(drawerState.selectedKegiatan.nama_kegiatan);
            html += `
                <span class="text-slate-400">/</span>
                <button type="button" onclick="goToStep(4)" class="font-semibold ${drawerState.currentStep === 4 ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600'} transition-colors max-w-[120px] truncate" title="${kegName}">
                    ${kegName}
                </button>
            `;
        }

        breadcrumbsEl.innerHTML = html;
    }

    // Step 1: Load Tahun Penetapan
    function loadStepTahun() {
        drawerState.currentStep = 1;
        stepBadge.textContent = '1';
        stepTitle.textContent = 'Pilih Tahun Penetapan Program';
        stepIndicator.textContent = '1 dari 4';
        backBtn.classList.add('hidden');
        searchInput.value = '';
        updateBreadcrumbs();

        listEl.innerHTML = '';
        emptyEl.classList.add('hidden');
        loadingEl.classList.remove('hidden');

        fetch("{{ route('api.master.tahun-program') }}")
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                loadingEl.classList.add('hidden');
                drawerState.dataTahun = data;
                renderTahunList(data);
            })
            .catch(err => {
                console.error(err);
                showDrawerError("Gagal memuat data. Muat ulang halaman atau hubungi administrator.");
            });
    }

    function renderTahunList(data) {
        listEl.innerHTML = '';
        if (!data || data.length === 0) {
            showDrawerEmpty();
            return;
        }

        data.forEach(year => {
            const isSelected = drawerState.selectedTahun == year;
            const card = document.createElement('div');
            card.className = `p-4 rounded-xl border transition-all cursor-pointer flex items-center justify-between ${isSelected ? 'border-indigo-600 bg-indigo-50/50 shadow-xs' : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50/80'} drawer-item`;
            card.setAttribute('data-name', year);
            card.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl ${isSelected ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'} flex items-center justify-center font-bold text-sm">
                        ${escapeHtml(year)}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Tahun Anggaran & Penetapan ${escapeHtml(year)}</h4>
                        <p class="text-[11px] text-slate-500">Klik untuk melihat program pada tahun ini</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            `;
            card.onclick = () => selectTahun(year);
            listEl.appendChild(card);
        });
    }

    function selectTahun(year) {
        drawerState.selectedTahun = year;
        drawerState.selectedProgram = null;
        drawerState.selectedKegiatan = null;
        drawerState.selectedSubKegiatan = null;
        loadStepProgram();
    }

    // Step 2: Load Programs for Selected Year
    function loadStepProgram() {
        drawerState.currentStep = 2;
        stepBadge.textContent = '2';
        stepTitle.textContent = `Pilih Program (Tahun ${drawerState.selectedTahun})`;
        stepIndicator.textContent = '2 dari 4';
        backBtn.classList.remove('hidden');
        searchInput.value = '';
        updateBreadcrumbs();

        listEl.innerHTML = '';
        emptyEl.classList.add('hidden');
        loadingEl.classList.remove('hidden');

        fetch(`{{ route('api.master.programs') }}?tahun=${drawerState.selectedTahun}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                loadingEl.classList.add('hidden');
                drawerState.dataPrograms = data;
                renderProgramsList(data);
            })
            .catch(err => {
                console.error(err);
                showDrawerError("Gagal memuat data. Muat ulang halaman atau hubungi administrator.");
            });
    }

    function renderProgramsList(data) {
        listEl.innerHTML = '';
        if (!data || data.length === 0) {
            showDrawerEmpty();
            return;
        }

        data.forEach(prog => {
            const isSelected = drawerState.selectedProgram && drawerState.selectedProgram.id === prog.id;
            const card = document.createElement('div');
            card.className = `p-4 rounded-xl border transition-all cursor-pointer flex items-center justify-between ${isSelected ? 'border-indigo-600 bg-indigo-50/50 shadow-xs' : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50/80'} drawer-item`;
            card.setAttribute('data-name', prog.nama_program);
            card.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 uppercase shrink-0">
                        Program
                    </span>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">${escapeHtml(prog.nama_program)}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Tahun ${escapeHtml(prog.tahun_penetapan)}</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            `;
            card.onclick = () => selectProgram(prog);
            listEl.appendChild(card);
        });
    }

    function selectProgram(prog) {
        drawerState.selectedProgram = prog;
        drawerState.selectedKegiatan = null;
        drawerState.selectedSubKegiatan = null;
        loadStepKegiatan();
    }

    // Step 3: Load Kegiatan for Selected Program
    function loadStepKegiatan() {
        drawerState.currentStep = 3;
        stepBadge.textContent = '3';
        stepTitle.textContent = 'Pilih Kegiatan';
        stepIndicator.textContent = '3 dari 4';
        backBtn.classList.remove('hidden');
        searchInput.value = '';
        updateBreadcrumbs();

        listEl.innerHTML = '';
        emptyEl.classList.add('hidden');
        loadingEl.classList.remove('hidden');

        fetch(`{{ route('api.master.kegiatans') }}?program_id=${drawerState.selectedProgram.id}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                loadingEl.classList.add('hidden');
                drawerState.dataKegiatans = data;
                renderKegiatansList(data);
            })
            .catch(err => {
                console.error(err);
                showDrawerError("Gagal memuat data. Muat ulang halaman atau hubungi administrator.");
            });
    }

    function renderKegiatansList(data) {
        listEl.innerHTML = '';
        if (!data || data.length === 0) {
            showDrawerEmpty();
            return;
        }

        data.forEach(keg => {
            const isSelected = drawerState.selectedKegiatan && drawerState.selectedKegiatan.id === keg.id;
            const card = document.createElement('div');
            card.className = `p-4 rounded-xl border transition-all cursor-pointer flex items-center justify-between ${isSelected ? 'border-emerald-600 bg-emerald-50/50 shadow-xs' : 'border-slate-200 bg-white hover:border-emerald-300 hover:bg-slate-50/80'} drawer-item`;
            card.setAttribute('data-name', keg.nama_kegiatan);
            card.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase shrink-0">
                        Kegiatan
                    </span>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">${escapeHtml(keg.nama_kegiatan)}</h4>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            `;
            card.onclick = () => selectKegiatan(keg);
            listEl.appendChild(card);
        });
    }

    function selectKegiatan(keg) {
        drawerState.selectedKegiatan = keg;
        drawerState.selectedSubKegiatan = null;
        loadStepSubKegiatan();
    }

    // Step 4: Load Sub Kegiatan for Selected Kegiatan
    function loadStepSubKegiatan() {
        drawerState.currentStep = 4;
        stepBadge.textContent = '4';
        stepTitle.textContent = 'Pilih Sub Kegiatan (Langkah Terakhir)';
        stepIndicator.textContent = '4 dari 4';
        backBtn.classList.remove('hidden');
        searchInput.value = '';
        updateBreadcrumbs();

        listEl.innerHTML = '';
        emptyEl.classList.add('hidden');
        loadingEl.classList.remove('hidden');

        fetch(`{{ route('api.master.sub-kegiatans') }}?kegiatan_id=${drawerState.selectedKegiatan.id}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                loadingEl.classList.add('hidden');
                drawerState.dataSubKegiatans = data;
                renderSubKegiatansList(data);
            })
            .catch(err => {
                console.error(err);
                showDrawerError("Gagal memuat data. Muat ulang halaman atau hubungi administrator.");
            });
    }

    function renderSubKegiatansList(data) {
        listEl.innerHTML = '';
        if (!data || data.length === 0) {
            showDrawerEmpty();
            return;
        }

        data.forEach(sub => {
            const card = document.createElement('div');
            card.className = `p-4 rounded-xl border border-slate-200 bg-white hover:border-purple-400 hover:bg-purple-50/50 hover:shadow-sm transition-all cursor-pointer flex items-center justify-between drawer-item`;
            card.setAttribute('data-name', sub.nama_sub_kegiatan);
            card.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 uppercase shrink-0">
                        Sub Kegiatan
                    </span>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">${escapeHtml(sub.nama_sub_kegiatan)}</h4>
                        <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-semibold text-purple-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Klik untuk memilih
                        </span>
                    </div>
                </div>
                <div class="p-1.5 rounded-lg bg-purple-100 text-purple-700 shrink-0 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            `;
            card.onclick = () => finishSelection(sub);
            listEl.appendChild(card);
        });
    }

    // Final Selection: Populate form inputs and close drawer
    function finishSelection(sub) {
        drawerState.selectedSubKegiatan = sub;

        // Populate hidden and visual form inputs
        const inputSubId = document.getElementById('sub_kegiatan_id');
        const inputProg = document.getElementById('program');
        const inputKeg = document.getElementById('kegiatan');
        const inputSub = document.getElementById('sub_kegiatan');

        if (inputSubId) inputSubId.value = sub.id;
        if (inputProg) inputProg.value = drawerState.selectedProgram.nama_program;
        if (inputKeg) inputKeg.value = drawerState.selectedKegiatan.nama_kegiatan;
        if (inputSub) inputSub.value = sub.nama_sub_kegiatan;

        // Visual UI Update in archive form
        if (typeof onProgramHierarchySelected === 'function') {
            onProgramHierarchySelected({
                subKegiatanId: sub.id,
                tahun: drawerState.selectedTahun,
                program: drawerState.selectedProgram.nama_program,
                kegiatan: drawerState.selectedKegiatan.nama_kegiatan,
                subKegiatan: sub.nama_sub_kegiatan,
            });
        }

        // Close drawer
        closeProgramPickerDrawer();
    }

    // Navigation & Back Handlers
    function drawerGoBack() {
        if (drawerState.currentStep === 4) {
            loadStepKegiatan();
        } else if (drawerState.currentStep === 3) {
            loadStepProgram();
        } else if (drawerState.currentStep === 2) {
            loadStepTahun();
        }
    }

    function goToStep(step) {
        if (step === 1) {
            loadStepTahun();
        } else if (step === 2 && drawerState.selectedTahun) {
            loadStepProgram();
        } else if (step === 3 && drawerState.selectedProgram) {
            loadStepKegiatan();
        } else if (step === 4 && drawerState.selectedKegiatan) {
            loadStepSubKegiatan();
        }
    }

    function renderCurrentStep() {
        if (drawerState.currentStep === 1) loadStepTahun();
        else if (drawerState.currentStep === 2) loadStepProgram();
        else if (drawerState.currentStep === 3) loadStepKegiatan();
        else if (drawerState.currentStep === 4) loadStepSubKegiatan();
    }

    // Client-side search within current step list
    function filterDrawerItems() {
        const query = searchInput.value.toLowerCase().trim();
        const items = listEl.querySelectorAll('.drawer-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            if (name.includes(query)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        if (visibleCount === 0 && items.length > 0) {
            emptyEl.classList.remove('hidden');
        } else {
            emptyEl.classList.add('hidden');
        }
    }
</script>
