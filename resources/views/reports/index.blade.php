<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Laporan Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
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

            <!-- Reports Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('reports.create') }}" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('Buat Laporan') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    @if(auth()->user()->hasRole('admin'))
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reports as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $report->report_date->format('d/m/Y') }}
                                        </td>
                                        @if(auth()->user()->hasRole('admin'))
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $report->user->name }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $report->project_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $report->location }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $report->start_time }} - {{ $report->end_time }}
                                            @if($report->is_overnight)
                                                <span class="text-xs text-indigo-600">(Lembur)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('reports.show', $report) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                            @can('update', $report)
                                                <a href="{{ route('reports.edit', $report) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                                            @endcan
                                            @can('delete', $report)
                                                <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('admin') ? 6 : 5 }}" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada laporan yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 