<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Laporan Pekerjaan') }}
            </h2>
            <a href="{{ route('reports.create') }}" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                {{ __('Buat Laporan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search/Filter Section -->
            <div class="mb-4">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row gap-4">
                    @if(auth()->user()->isAdmin())
                    <div class="flex-1" x-data="{ 
                        search: '{{ request('employee_search') }}',
                        items: {{ $employees }},
                        filteredItems: [],
                        showDropdown: false,
                        init() {
                            this.filteredItems = this.items;
                            this.$watch('search', (value) => {
                                this.filteredItems = this.items.filter(item => 
                                    item.toLowerCase().includes(value.toLowerCase())
                                );
                                this.showDropdown = value.length > 0;
                            });
                        }
                    }">
                        <x-input-label for="employee_search" :value="__('Nama Karyawan')" />
                        <div class="relative">
                            <input type="text" 
                                id="employee_search"
                                name="employee_search"
                                x-model="search"
                                @focus="showDropdown = true"
                                @click.away="showDropdown = false"
                                placeholder="Cari berdasarkan nama karyawan..." 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            
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

                    <!-- Filter lainnya tetap ditampilkan -->
                    <div class="flex-1">
                        <x-input-label for="report_date" :value="__('Tanggal')" />
                        <input type="date" 
                            id="report_date"
                            name="report_date"
                            value="{{ request('report_date') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex-1" x-data="{ 
                        search: '{{ request('location') }}',
                        items: {{ $locations }},
                        filteredItems: [],
                        showDropdown: false,
                        init() {
                            this.filteredItems = this.items;
                            this.$watch('search', (value) => {
                                this.filteredItems = this.items.filter(item => 
                                    item.toLowerCase().includes(value.toLowerCase())
                                );
                                this.showDropdown = value.length > 0;
                            });
                        }
                    }">
                        <x-input-label for="location" :value="__('Lokasi')" />
                        <div class="relative">
                            <input type="text" 
                                id="location"
                                name="location"
                                x-model="search"
                                @focus="showDropdown = true"
                                @click.away="showDropdown = false"
                                placeholder="Cari berdasarkan lokasi..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            
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

                    <div class="flex-1" x-data="{ 
                        search: '{{ request('project_code') }}',
                        items: {{ $projectCodes }},
                        filteredItems: [],
                        showDropdown: false,
                        init() {
                            this.filteredItems = this.items;
                            this.$watch('search', (value) => {
                                this.filteredItems = this.items.filter(item => 
                                    item.toLowerCase().includes(value.toLowerCase())
                                );
                                this.showDropdown = value.length > 0;
                            });
                        }
                    }">
                        <x-input-label for="project_code" :value="__('Project')" />
                        <div class="relative">
                            <input type="text" 
                                id="project_code"
                                name="project_code"
                                x-model="search"
                                @focus="showDropdown = true"
                                @click.away="showDropdown = false"
                                placeholder="Cari berdasarkan kode project..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            
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

                    <div class="w-full sm:w-auto flex gap-2">
                        <button type="submit" 
                            class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            {{ __('Cari') }}
                        </button>
                        @if(request()->hasAny(['employee_search', 'report_date', 'location', 'project_code']))
                            <a href="{{ route('reports.index') }}" 
                                class="flex-1 sm:flex-none px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 inline-flex items-center justify-center">
                                {{ __('Reset') }}
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
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $report->report_date->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</div>
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->user->name }}</td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->project_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->location }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ substr($report->start_time, 0, 5) }} - {{ substr($report->end_time, 0, 5) }}
                                            </div>
                                            <div class="flex gap-1 mt-1">
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
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('reports.show', $report) }}" 
                                                    class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                                <a href="{{ route('reports.export', $report) }}" 
                                                    class="text-green-600 hover:text-green-900">Export</a>
                                                @can('update', $report)
                                                    <a href="{{ route('reports.edit', $report) }}" 
                                                        class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                @endcan
                                                @can('delete', $report)
                                                    <!-- Delete Button and Modal -->
                                                    <div x-data="{ showModal: false }">
                                                        <button @click="showModal = true" 
                                                            class="text-red-600 hover:text-red-900">
                                                            Hapus
                                                        </button>

                                                        <!-- Modal Backdrop -->
                                                        <div x-show="showModal" 
                                                            x-transition:enter="transition ease-out duration-300"
                                                            x-transition:enter-start="opacity-0"
                                                            x-transition:enter-end="opacity-100"
                                                            x-transition:leave="transition ease-in duration-200"
                                                            x-transition:leave-start="opacity-100"
                                                            x-transition:leave-end="opacity-0"
                                                            class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 flex items-center justify-center"
                                                            @click="showModal = false">

                                                            <!-- Modal Content -->
                                                            <div x-show="showModal" 
                                                                x-transition:enter="transition ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 transform scale-90"
                                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                                x-transition:leave="transition ease-in duration-200"
                                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                                x-transition:leave-end="opacity-0 transform scale-90"
                                                                @click.stop
                                                                class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                                                                
                                                                <!-- Modal Header -->
                                                                <div class="p-4 border-b">
                                                                    <h3 class="text-lg font-medium text-gray-900 text-left">Konfirmasi Penghapusan</h3>
                                                                </div>

                                                                <!-- Modal Body -->
                                                                <div class="p-4">
                                                                    <div class="flex items-start mb-4">
                                                                        <div class="flex-shrink-0 bg-red-100 rounded-full p-2 mr-3">
                                                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                                            </svg>
                                                                        </div>
                                                                        <p class="text-gray-600">Apakah Anda yakin ingin menghapus laporan ini?</p>
                                                                    </div>
                                                                    <div class="text-sm text-gray-500 bg-gray-50 rounded p-3">
                                                                        <div class="text-left">
                                                                            <p>Tanggal: {{ $report->report_date->format('d/m/Y') }}</p>
                                                                            <p>Project: {{ $report->project_code }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Modal Footer -->
                                                                <div class="p-4 border-t flex justify-end space-x-3">
                                                                    <button type="button" @click="showModal = false"
                                                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                                                                        Batal
                                                                    </button>
                                                                    <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                                                                            Hapus Laporan
                                                                        </button>
                                                                    </form>
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
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4 space-y-3">
                            <!-- Header -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $report->report_date->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</div>
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
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('reports.show', $report) }}" 
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-md text-sm font-medium hover:bg-indigo-100">
                                    <span>Lihat Detail</span>
                                    <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                                <a href="{{ route('reports.export', $report) }}" 
                                    class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-md text-sm font-medium hover:bg-green-100">
                                    <span>Export</span>
                                    <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
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
                                    <!-- Delete Button and Modal -->
                                    <div x-data="{ showModal: false }">
                                        <button @click="showModal = true" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-md text-sm font-medium hover:bg-red-100">
                                            <span>Hapus</span>
                                        </button>

                                        <!-- Modal Backdrop -->
                                        <div x-show="showModal" 
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 flex items-center justify-center"
                                            @click="showModal = false">

                                            <!-- Modal Content -->
                                            <div x-show="showModal" 
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 transform scale-90"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-90"
                                                @click.stop
                                                class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                                                
                                                <!-- Modal Header -->
                                                <div class="p-4 border-b">
                                                    <h3 class="text-lg font-medium text-gray-900 text-left">Konfirmasi Penghapusan</h3>
                                                </div>

                                                <!-- Modal Body -->
                                                <div class="p-4">
                                                    <div class="flex items-start mb-4">
                                                        <div class="flex-shrink-0 bg-red-100 rounded-full p-2 mr-3">
                                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                            </svg>
                                                        </div>
                                                        <p class="text-gray-600">Apakah Anda yakin ingin menghapus laporan ini?</p>
                                                    </div>
                                                    <div class="text-sm text-gray-500 bg-gray-50 rounded p-3">
                                                        <div class="text-left">
                                                            <p>Tanggal: {{ $report->report_date->format('d/m/Y') }}</p>
                                                            <p>Project: {{ $report->project_code }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal Footer -->
                                                <div class="p-4 border-t flex justify-end space-x-3">
                                                    <button type="button" @click="showModal = false"
                                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                                                        Batal
                                                    </button>
                                                    <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                                                            Hapus Laporan
                                                        </button>
                                                    </form>
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