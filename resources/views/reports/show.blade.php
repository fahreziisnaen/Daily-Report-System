<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Laporan') }}
            </h2>
            <div class="flex space-x-2">
                @if($report->is_overtime)
                    <a href="{{ route('reports.export', $report) }}" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('Lembur') }}
                    </a>
                @endif
                @can('update', $report)
                    <a href="{{ route('reports.edit', $report) }}" 
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                        {{ __('Edit') }}
                    </a>
                @endcan
                @can('delete', $report)
                    <!-- Delete Button and Modal -->
                    <div x-data="{ showModal: false }">
                        <!-- Trigger Button -->
                        <button @click="showModal = true" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            {{ __('Hapus') }}
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
                                    <h3 class="text-lg font-medium text-gray-900">Konfirmasi Penghapusan</h3>
                                </div>

                                <!-- Modal Body -->
                                <div class="p-4">
                                    <div class="flex items-center mb-4">
                                        <div class="flex-shrink-0 bg-red-100 rounded-full p-2 mr-3">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-600">Apakah Anda yakin ingin menghapus laporan ini?</p>
                                    </div>
                                    <div class="text-sm text-gray-500 bg-gray-50 rounded p-3">
                                        <p><span class="font-medium">Tanggal:</span> {{ $report->report_date->format('d/m/Y') }}</p>
                                        <p><span class="font-medium">Project:</span> {{ $report->project_code }}</p>
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
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-gray-500">Tanggal</div>
                                <div class="mt-1">{{ $report->report_date->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Pekerja</div>
                                <div class="mt-1">{{ $report->user->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Project</div>
                                <div class="mt-1">{{ $report->project_code }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Lokasi</div>
                                <div class="mt-1">{{ $report->location }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Waktu</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-gray-500">Waktu Mulai</div>
                                <div class="mt-1">{{ substr($report->start_time, 0, 5) }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Waktu Selesai</div>
                                <div class="mt-1">{{ substr($report->end_time, 0, 5) }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Status</div>
                                <div class="mt-1 flex gap-2">
                                    @if($report->is_overtime)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Overtime
                                        </span>
                                    @endif
                                    @if($report->is_overnight)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Overnight
                                        </span>
                                    @endif
                                    @if($report->is_shift)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Pergantian Shift
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->work_day_type === 'Hari Libur' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $report->work_day_type }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Pekerjaan</h3>
                        <div class="space-y-4">
                            @foreach($report->details as $index => $detail)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="text-sm font-medium text-gray-900">Detail #{{ $index + 1 }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $detail->status === 'Selesai' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $detail->status === 'Dalam Proses' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $detail->status === 'Tertunda' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $detail->status === 'Bermasalah' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $detail->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $detail->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="border-t pt-4 mt-6">
                        <div class="flex flex-col space-y-2 text-sm text-gray-500">
                            <div class="flex justify-between">
                                <div>
                                    Dibuat: {{ $report->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                    oleh {{ $report->user->name }}
                                </div>
                            </div>
                            @if($report->created_at != $report->updated_at)
                                <div class="flex justify-between">
                                    <div>
                                        Terakhir diupdate: {{ $report->updated_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                        oleh {{ $report->updater ? $report->updater->name : 'Unknown' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="flex justify-end pt-4">
                        <a href="{{ route('reports.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 