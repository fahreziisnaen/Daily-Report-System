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
            <!-- Filter Section - Collapsible on mobile -->
            <div x-data="{ showFilters: false }" class="mb-6">
                <button @click="showFilters = !showFilters" 
                    class="md:hidden w-full flex items-center justify-between p-4 bg-white rounded-lg shadow">
                    <span>Filter</span>
                    <svg class="w-5 h-5" :class="{'rotate-180': showFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div :class="{'hidden': !showFilters}" class="md:block mt-4 md:mt-0">
                    <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div x-data="{
                                type: 'employee',
                                searchTerm: '{{ request('employee_search') }}',
                                results: [],
                                open: false,

                                async search() {
                                    if (this.searchTerm.length < 2) {
                                        this.results = [];
                                        this.open = false;
                                        return;
                                    }

                                    const endpoints = {
                                        'employee': '/api/search-employees',
                                        'location': '/api/search-locations',
                                        'project': '/api/search-projects'
                                    };

                                    try {
                                        const response = await fetch(`${endpoints[this.type]}?q=${encodeURIComponent(this.searchTerm)}`);
                                        this.results = await response.json();
                                        this.open = true;
                                    } catch (error) {
                                        console.error('Search error:', error);
                                        this.results = [];
                                    }
                                },

                                select(result) {
                                    this.searchTerm = result;
                                    this.open = false;
                                }
                            }">
                                <label class="block text-sm font-medium text-gray-700">Nama Karyawan</label>
                                <div class="relative">
                                    <input type="text" 
                                        x-model="searchTerm"
                                        @input="search()"
                                        @click="open = true"
                                        name="employee_search"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Cari karyawan..."
                                        autocomplete="off">
                                    
                                    <!-- Dropdown -->
                                    <div x-show="open && results.length > 0" 
                                        @click.away="open = false"
                                        class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200">
                                        <ul class="max-h-60 rounded-md py-1 text-base overflow-auto">
                                            <template x-for="result in results" :key="result">
                                                <li @click="select(result)"
                                                    class="cursor-pointer hover:bg-indigo-500 hover:text-white px-3 py-2"
                                                    x-text="result">
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <input type="date" 
                                    name="report_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ request('report_date') }}">
                            </div>

                            <div x-data="{
                                type: 'location',
                                searchTerm: '{{ request('location') }}',
                                results: [],
                                open: false,

                                async search() {
                                    if (this.searchTerm.length < 2) {
                                        this.results = [];
                                        this.open = false;
                                        return;
                                    }

                                    const endpoints = {
                                        'employee': '/api/search-employees',
                                        'location': '/api/search-locations',
                                        'project': '/api/search-projects'
                                    };

                                    try {
                                        const response = await fetch(`${endpoints[this.type]}?q=${encodeURIComponent(this.searchTerm)}`);
                                        this.results = await response.json();
                                        this.open = true;
                                    } catch (error) {
                                        console.error('Search error:', error);
                                        this.results = [];
                                    }
                                },

                                select(result) {
                                    this.searchTerm = result;
                                    this.open = false;
                                }
                            }">
                                <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                <div class="relative">
                                    <input type="text" 
                                        x-model="searchTerm"
                                        @input="search()"
                                        @click="open = true"
                                        name="location"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Cari lokasi..."
                                        value="{{ request('location') }}"
                                        autocomplete="off">
                                    
                                    <!-- Dropdown -->
                                    <div x-show="open && results.length > 0" 
                                        @click.away="open = false"
                                        class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200">
                                        <ul class="max-h-60 rounded-md py-1 text-base overflow-auto">
                                            <template x-for="result in results" :key="result">
                                                <li @click="select(result)"
                                                    class="cursor-pointer hover:bg-indigo-500 hover:text-white px-3 py-2"
                                                    x-text="result">
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div x-data="{
                                type: 'project',
                                searchTerm: '{{ request('project_code') }}',
                                results: [],
                                open: false,

                                async search() {
                                    if (this.searchTerm.length < 2) {
                                        this.results = [];
                                        this.open = false;
                                        return;
                                    }

                                    const endpoints = {
                                        'employee': '/api/search-employees',
                                        'location': '/api/search-locations',
                                        'project': '/api/search-projects'
                                    };

                                    try {
                                        const response = await fetch(`${endpoints[this.type]}?q=${encodeURIComponent(this.searchTerm)}`);
                                        this.results = await response.json();
                                        this.open = true;
                                    } catch (error) {
                                        console.error('Search error:', error);
                                        this.results = [];
                                    }
                                },

                                select(result) {
                                    this.searchTerm = result;
                                    this.open = false;
                                }
                            }">
                                <label class="block text-sm font-medium text-gray-700">Kode Project</label>
                                <div class="relative">
                                    <input type="text" 
                                        x-model="searchTerm"
                                        @input="search()"
                                        @click="open = true"
                                        name="project_code"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Cari kode project..."
                                        value="{{ request('project_code') }}"
                                        autocomplete="off">
                                    
                                    <!-- Dropdown -->
                                    <div x-show="open && results.length > 0" 
                                        @click.away="open = false"
                                        class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200">
                                        <ul class="max-h-60 rounded-md py-1 text-base overflow-auto">
                                            <template x-for="result in results" :key="result">
                                                <li @click="select(result)"
                                                    class="cursor-pointer hover:bg-indigo-500 hover:text-white px-3 py-2"
                                                    x-text="result">
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Filters -->
                        @if(request()->hasAny(['employee_search', 'report_date', 'location', 'project_code']))
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if(request('employee_search'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        Karyawan: {{ request('employee_search') }}
                                    </span>
                                @endif
                                @if(request('report_date'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        Tanggal: {{ \Carbon\Carbon::parse(request('report_date'))->format('d/m/Y') }}
                                    </span>
                                @endif
                                @if(request('location'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        Lokasi: {{ request('location') }}
                                    </span>
                                @endif
                                @if(request('project_code'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        Project: {{ request('project_code') }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('reports.index') }}" 
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                                Reset
                            </a>
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    @if(auth()->user()->isAdmin())
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pekerja</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->project_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->location }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ substr($report->start_time, 0, 5) }} - {{ substr($report->end_time, 0, 5) }}
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->user->name }}</td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->work_day_type }}</td>
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
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <div class="text-gray-500">Waktu</div>
                                    <div class="font-medium">{{ substr($report->start_time, 0, 5) }} - {{ substr($report->end_time, 0, 5) }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Status</div>
                                    <div class="font-medium">{{ $report->work_day_type }}</div>
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