<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Daftar Laporan Pekerjaan') }}
            </h2>
            <div class="flex space-x-2">
                @can('admin')
                <a href="{{ route('admin.projects.index') }}" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    {{ __('MANAGE PROJECT') }}
                </a>
                @endcan
                <a href="{{ route('reports.create') }}" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    {{ __('BUAT LAPORAN') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search/Filter Section -->
            <div class="mb-4">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
                    @if(auth()->user()->isAdmin())
                    <div class="flex-1">
                        <x-input-label for="employee_search" :value="__('Nama Karyawan')" class="mb-1" />
                        <div class="relative" x-data="{ 
                            search: '{{ request('employee_search') }}',
                            items: {{ $employees }},
                            filteredItems: [],
                            showDropdown: false,
                            init() {
                                this.filteredItems = this.items;
                                this.$watch('search', (value) => {
                                    if (value.length > 0) {
                                        this.filteredItems = this.items.filter(item => 
                                            item.toLowerCase().includes(value.toLowerCase())
                                        );
                                        this.showDropdown = true;
                                    } else {
                                        this.showDropdown = false;
                                    }
                                });
                            }
                        }">
                            <input type="text" 
                                id="employee_search"
                                name="employee_search"
                                x-model="search"
                                @focus="showDropdown = search.length > 0"
                                @click.away="showDropdown = false"
                                placeholder="Cari berdasarkan nama karyawan..." 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-[38px]">
                            
                            <!-- Dropdown -->
                            <div x-show="showDropdown" 
                                x-transition
                                class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-auto">
                                <template x-for="item in filteredItems" :key="item">
                                    <div @click="search = item; showDropdown = false"
                                        x-text="item"
                                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                                        :class="{'bg-gray-50': search === item}">
                                    </div>
                                </template>
                                <div x-show="filteredItems.length === 0" 
                                    class="px-4 py-2 text-sm text-gray-500">
                                    Tidak ada hasil yang cocok
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Filter tanggal -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <div class="relative" x-data="{ 
                            date: '{{ request('report_date', '') }}',
                            formattedDate: '{{ request('report_date') ? \Carbon\Carbon::parse(request('report_date'))->format('d/m/Y') : '' }}'
                        }">
                            <input type="date" 
                                name="report_date" 
                                id="report_date"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base date-input h-[38px]" 
                                x-model="date"
                                @change="formattedDate = date ? new Date(date).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}) : ''"
                                :data-date="formattedDate || 'Pilih tanggal...'"
                                placeholder="Pilih tanggal...">
                            <style>
                                .date-input::-webkit-calendar-picker-indicator {
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
                                    z-index: 10;
                                }
                                .date-input::before {
                                    content: attr(data-date);
                                    position: absolute;
                                    left: 0;
                                    right: 0;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    padding: 0 0.75rem;
                                    pointer-events: none;
                                    z-index: 1;
                                    color: #6B7280;  /* Default placeholder color */
                                }
                                .date-input:not([data-date=""]):before,
                                .date-input[data-date]:not([data-date="Pilih tanggal..."]):before {
                                    color: #111827;  /* Text color when date is selected */
                                }
                                .date-input {
                                    color: transparent !important;
                                    background: white;
                                    padding: 0.5rem 0.75rem !important;
                                }
                                .date-input::placeholder {
                                    color: #6B7280;
                                    opacity: 1;
                                }
                            </style>
                        </div>
                    </div>

                    <!-- Filter Lokasi -->
                    <div class="flex-1">
                        <x-input-label for="location" :value="__('Lokasi')" class="mb-1" />
                        <div class="relative" x-data="{ 
                            search: '{{ request('location') }}',
                            items: {{ $locations }},
                            filteredItems: [],
                            showDropdown: false,
                            init() {
                                this.filteredItems = this.items;
                                this.$watch('search', (value) => {
                                    if (value.length > 0) {
                                        this.filteredItems = this.items.filter(item => 
                                            item.toLowerCase().includes(value.toLowerCase())
                                        );
                                        this.showDropdown = true;
                                    } else {
                                        this.showDropdown = false;
                                    }
                                });
                            }
                        }">
                            <input type="text" 
                                id="location"
                                name="location"
                                x-model="search"
                                @focus="showDropdown = search.length > 0"
                                @click.away="showDropdown = false"
                                placeholder="Cari berdasarkan lokasi..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-[38px]">
                            
                            <!-- Dropdown -->
                            <div x-show="showDropdown" 
                                x-transition
                                class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-auto">
                                <template x-for="item in filteredItems" :key="item">
                                    <div @click="search = item; showDropdown = false"
                                        x-text="item"
                                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                                        :class="{'bg-gray-50': search === item}">
                                    </div>
                                </template>
                                <div x-show="filteredItems.length === 0" 
                                    class="px-4 py-2 text-sm text-gray-500">
                                    Tidak ada hasil yang cocok
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Project -->
                    <div class="flex-1">
                        <x-input-label for="project_code" :value="__('Project')" class="mb-1" />
                        <div class="relative" x-data="{ 
                            search: '{{ request('project_code') }}',
                            items: {{ $projectCodes }},
                            filteredItems: [],
                            showDropdown: false,
                            init() {
                                this.filteredItems = this.items;
                                this.$watch('search', (value) => {
                                    if (value.length > 0) {
                                        this.filteredItems = this.items.filter(item => 
                                            item.toLowerCase().includes(value.toLowerCase())
                                        );
                                        this.showDropdown = true;
                                    } else {
                                        this.showDropdown = false;
                                    }
                                });
                            }
                        }">
                            <input type="text" 
                                id="project_code"
                                name="project_code"
                                x-model="search"
                                @focus="showDropdown = search.length > 0"
                                @click.away="showDropdown = false"
                                placeholder="Cari berdasarkan kode project..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-[38px]">
                            
                            <!-- Dropdown -->
                            <div x-show="showDropdown" 
                                x-transition
                                class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-auto">
                                <template x-for="item in filteredItems" :key="item">
                                    <div @click="search = item; showDropdown = false"
                                        x-text="item"
                                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                                        :class="{'bg-gray-50': search === item}">
                                    </div>
                                </template>
                                <div x-show="filteredItems.length === 0" 
                                    class="px-4 py-2 text-sm text-gray-500">
                                    Tidak ada hasil yang cocok
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 items-stretch h-[38px]">
                        <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200 flex items-center justify-center min-w-[80px]">
                            <span>Cari</span>
                        </button>
                        @if(request()->hasAny(['employee_search', 'report_date', 'location', 'project_code']))
                            <a href="{{ route('reports.index') }}" 
                                class="px-4 py-2 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition-colors duration-200 flex items-center justify-center min-w-[80px] border border-red-200">
                                <span>Reset</span>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Reports List -->
            <!-- Desktop View (Hidden on Mobile) -->
            <div class="hidden md:block">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    @if(auth()->user()->isAdmin())
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pekerja</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reports as $report)
                                    <tr x-data="{ showDeleteModal: false }">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $report->report_date->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">
                                                @if($report->created_at == $report->updated_at)
                                                    {{ $report->created_at->diffForHumans() }}
                                                @else
                                                    Diedit {{ $report->updated_at->diffForHumans() }}
                                                @endif
                                            </div>
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->user->name }}</td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->project_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->location }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="text-sm text-gray-900">
                                                {{ substr($report->start_time, 0, 5) }} - {{ substr($report->end_time, 0, 5) }}
                                            </div>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if($report->is_overtime)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Overtime
                                                    </span>
                                                @endif
                                                @if($report->is_overnight)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        Overnight
                                                    </span>
                                                @endif
                                                @if($report->is_shift)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Pergantian Shift
                                                    </span>
                                                @endif
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $report->work_day_type === 'Hari Libur' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $report->work_day_type }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('reports.show', $report) }}" 
                                                    class="text-indigo-600 hover:text-indigo-800">
                                                    View
                                                </a>
                                                @if($report->is_overtime)
                                                    <a href="{{ route('reports.export', $report) }}" 
                                                        class="text-green-600 hover:text-green-800 font-medium">
                                                        Lembur
                                                    </a>
                                                @endif
                                                @can('update', $report)
                                                    <a href="{{ route('reports.edit', $report) }}" 
                                                        class="text-yellow-600 hover:text-yellow-800">
                                                        Edit
                                                    </a>
                                                @endcan
                                                @can('delete', $report)
                                                    <button type="button"
                                                        @click="showDeleteModal = true" 
                                                        class="text-red-600 hover:text-red-800">
                                                        Hapus
                                                    </button>

                                                    <!-- Delete Confirmation Modal -->
                                                    <div x-show="showDeleteModal" 
                                                        class="fixed inset-0 z-50 overflow-y-auto" 
                                                        style="display: none;">
                                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                                            </div>
                                                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                    <div class="sm:flex sm:items-start">
                                                                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                                                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                                                Konfirmasi Hapus
                                                                            </h3>
                                                                            <div class="mt-2">
                                                                                <p class="text-sm text-gray-500">
                                                                                    Apakah Anda yakin ingin menghapus laporan ini?
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                    <form action="{{ route('reports.destroy', $report) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                                            Hapus
                                                                        </button>
                                                                    </form>
                                                                    <button type="button" 
                                                                        @click="showDeleteModal = false"
                                                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                        Batal
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mobile View (Hidden on Desktop) -->
            <div class="md:hidden space-y-4">
                @foreach($reports as $report)
                    <div x-data="{ showDeleteModal: false }" class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4 space-y-3">
                            <!-- Header -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $report->report_date->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if($report->created_at == $report->updated_at)
                                            {{ $report->created_at->diffForHumans() }}
                                        @else
                                            Diedit {{ $report->updated_at->diffForHumans() }}
                                        @endif
                                    </div>
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <div class="text-sm text-gray-600">{{ $report->user->name }}</div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <div class="text-gray-500">Project</div>
                                    <div class="font-medium">{{ $report->project_code }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Lokasi</div>
                                    <div class="font-medium">{{ $report->location }}</div>
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="text-sm">
                                <div class="text-gray-500">Waktu</div>
                                <div class="font-medium flex items-center gap-2">
                                    <span>{{ substr($report->start_time, 0, 5) }} - {{ substr($report->end_time, 0, 5) }}</span>
                                    @if($report->is_overtime)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Overtime
                                        </span>
                                    @endif
                                    @if($report->is_overnight)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            Overnight
                                        </span>
                                    @endif
                                    @if($report->is_shift)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Pergantian Shift
                                        </span>
                                    @endif
                                    @if($report->work_day_type === 'Hari Libur')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Hari Libur
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('reports.show', $report) }}" 
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-md text-sm font-medium hover:bg-indigo-100">
                                    <span>View</span>
                                    <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($report->is_overtime)
                                    <a href="{{ route('reports.export', $report) }}" 
                                        class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-md text-sm font-medium hover:bg-green-100"
                                        title="Download Lembur">
                                        <span>Lembur</span>
                                        <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                @endif
                                @can('update', $report)
                                    <a href="{{ route('reports.edit', $report) }}" 
                                        class="inline-flex items-center px-3 py-1.5 bg-yellow-50 text-yellow-600 rounded-md text-sm font-medium hover:bg-yellow-100">
                                        <span>Edit</span>
                                        <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endcan
                                @can('delete', $report)
                                    <button type="button"
                                        @click="showDeleteModal = true"
                                        class="text-red-600 hover:text-red-800">
                                        Hapus
                                    </button>

                                    <!-- Delete Confirmation Modal (sama seperti di atas) -->
                                    <div x-show="showDeleteModal" 
                                        class="fixed inset-0 z-50 overflow-y-auto" 
                                        style="display: none;">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                            </div>
                                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                                Konfirmasi Hapus
                                                            </h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500">
                                                                    Apakah Anda yakin ingin menghapus laporan ini?
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <form action="{{ route('reports.destroy', $report) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                    <button type="button" 
                                                        @click="showDeleteModal = false"
                                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $reports->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</x-app-layout> 