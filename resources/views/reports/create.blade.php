<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Laporan Baru') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4 pb-24 md:pb-4">
                    <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pekerjaan</h3>
                            
                            <!-- Tanggal -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <div class="relative">
                                    <input type="date" 
                                        name="report_date" 
                                        id="report_date"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                                        value="{{ old('report_date', date('Y-m-d')) }}"
                                        required
                                        onchange="this.setAttribute('data-date', this.value);"
                                        style="position: relative; height: 38px;"
                                        data-date="{{ old('report_date', date('Y-m-d')) }}">
                                    <style>
                                        input[type="date"]::-webkit-calendar-picker-indicator {
                                            background: transparent;
                                            bottom: 0;
                                            color: transparent;
                                            cursor: pointer;
                                            height: 100%;
                                            left: 0;
                                            position: absolute;
                                            right: 0;
                                            top: 0;
                                            width: auto;
                                        }
                                        input[type="date"]::before {
                                            content: attr(data-date);
                                            color: #000000;
                                            padding: 0.5rem 0.75rem;
                                            position: absolute;
                                            left: 0;
                                            top: 50%;
                                            transform: translateY(-50%);
                                        }
                                        input[type="date"] {
                                            color: transparent;
                                            padding: 0.5rem 0.75rem;
                                            height: 38px;
                                            display: flex;
                                            align-items: center;
                                        }
                                    </style>
                                    @error('report_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kode Project -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Project</label>
                                <select name="project_code" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base"
                                    required>
                                    <option value="">Pilih Project</option>
                                </select>
                            </div>

                            <!-- Lokasi -->
                            <div class="mb-4" x-data="{ locationType: 'homebase' }">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <label class="flex items-center space-x-2 p-2 bg-white rounded-md border border-gray-200">
                                        <input type="radio" 
                                            name="location_type" 
                                            value="homebase" 
                                            x-model="locationType"
                                            class="w-5 h-5 text-indigo-600">
                                        <span class="text-sm">Homebase</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white rounded-md border border-gray-200">
                                        <input type="radio" 
                                            name="location_type" 
                                            value="dinas" 
                                            x-model="locationType"
                                            class="w-5 h-5 text-indigo-600">
                                        <span class="text-sm">Lokasi Dinas</span>
                                    </label>
                                </div>
                                
                                <!-- Hidden input untuk homebase -->
                                <input type="hidden" 
                                    name="location" 
                                    x-bind:value="locationType === 'homebase' ? '{{ auth()->user()->homebase }}' : ''">
                                
                                <!-- Input lokasi dinas yang muncul saat pilih dinas -->
                                <div x-show="locationType === 'dinas'" 
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Lokasi Dinas</label>
                                    <input type="text" 
                                        x-bind:name="locationType === 'dinas' ? 'location' : ''"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base"
                                        placeholder="Masukkan lokasi dinas"
                                        x-bind:required="locationType === 'dinas'">
                                </div>
                            </div>

                            <!-- Waktu -->
                            <div class="mb-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                                        <input type="time" name="start_time" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base"
                                            value="{{ old('start_time') }}"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                                        <input type="time" name="end_time" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base"
                                            value="{{ old('end_time') }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- Kerja Saat -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kerja Saat</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center space-x-2 p-2 bg-white rounded-md border border-gray-200">
                                        <input type="radio" 
                                            name="work_day_type" 
                                            value="Hari Kerja" 
                                            id="work_day_type_kerja"
                                            class="w-5 h-5 text-indigo-600">
                                        <span class="text-sm">Hari Kerja</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white rounded-md border border-gray-200">
                                        <input type="radio" 
                                            name="work_day_type" 
                                            value="Hari Libur" 
                                            id="work_day_type_libur"
                                            class="w-5 h-5 text-indigo-600">
                                        <span class="text-sm">Hari Libur</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Overnight -->
                            <div class="flex flex-wrap gap-4 mt-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" 
                                        name="is_overnight" 
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                                        {{ isset($report) && $report->is_overnight ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">Overnight (Lanjut hari berikutnya)</span>
                                </label>

                                <!-- Tambahkan checkbox Shift -->
                                <label class="inline-flex items-center">
                                    <input type="checkbox" 
                                        name="is_shift" 
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                                        {{ isset($report) && $report->is_shift ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">Pergantian Shift</span>
                                </label>
                            </div>
                        </div>

                        <!-- Work Details Section -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Detail Pekerjaan</h3>
                                <button type="button" onclick="addWorkDetail()"
                                    class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                            <div id="work-details" class="space-y-4">
                                <!-- Work details will be added here -->
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="fixed bottom-0 left-0 right-0 bg-white/95 border-t border-gray-200 p-4 z-50 backdrop-blur-sm md:relative md:border-0 md:bg-transparent md:p-0 md:backdrop-blur-none">
                            <div class="flex justify-end space-x-3 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <a href="{{ route('reports.index') }}" 
                                    class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                                    Batal
                                </a>
                                <button type="submit" 
                                    class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let detailCount = 0;

        function addWorkDetail() {
            const container = document.getElementById('work-details');
            const detail = document.createElement('div');
            detail.className = 'p-4 bg-white rounded-lg border border-gray-200 relative';
            detail.innerHTML = `
                <button type="button" onclick="this.parentElement.remove()" 
                    class="absolute right-2 top-2 text-gray-400 hover:text-red-500 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="space-y-4 pr-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uraian Pekerjaan</label>
                        <textarea name="work_details[${detailCount}][description]" rows="2"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base"
                            required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="work_details[${detailCount}][status]"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base"
                            required>
                            <option value="Selesai">Selesai</option>
                            <option value="Dalam Proses">Dalam Proses</option>
                            <option value="Tertunda">Tertunda</option>
                            <option value="Bermasalah">Bermasalah</option>
                        </select>
                    </div>
                </div>
            `;
            container.appendChild(detail);
            detailCount++;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('report_date');
            const workDayKerja = document.getElementById('work_day_type_kerja');
            const workDayLibur = document.getElementById('work_day_type_libur');
            
            function checkIfSunday(date) {
                const day = new Date(date).getDay();
                if (day === 0) { // 0 adalah hari Minggu
                    workDayLibur.checked = true;
                } else {
                    workDayKerja.checked = true;
                }
            }

            function formatDateForDisplay(dateString) {
                if (!dateString) return '';
                const [year, month, day] = dateString.split('-');
                return `${day}/${month}/${year}`;
            }

            // Format tanggal saat halaman dimuat
            dateInput.setAttribute('data-date', formatDateForDisplay(dateInput.value));

            // Update format dan cek hari Minggu saat tanggal berubah
            dateInput.addEventListener('change', function() {
                this.setAttribute('data-date', formatDateForDisplay(this.value));
                checkIfSunday(this.value);
            });

            // Cek hari Minggu saat halaman dimuat
            checkIfSunday(dateInput.value);

            // Add first detail automatically
            if (document.getElementById('work-details').children.length === 0) {
                addWorkDetail();
            }

            // Fetch active projects
            fetch('/api/active-projects')
                .then(response => response.json())
                .then(projects => {
                    const projectSelect = document.querySelector('[name="project_code"]');
                    projects.forEach(project => {
                        const option = document.createElement('option');
                        option.value = project.code;
                        option.textContent = project.code;
                        projectSelect.appendChild(option);
                    });
                });
        });
    </script>
    @endpush
</x-app-layout> 