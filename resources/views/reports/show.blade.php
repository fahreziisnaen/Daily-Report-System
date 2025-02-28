<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Laporan') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.export', $report) }}" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    {{ __('Export') }}
                </a>
                @can('update', $report)
                    <a href="{{ route('reports.edit', $report) }}" 
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                        {{ __('Edit') }}
                    </a>
                @endcan
                @can('delete', $report)
                    <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                            {{ __('Hapus') }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pekerjaan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $report->report_date->format('d/m/Y') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kode Project</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $report->project_code }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $report->location }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kerja Saat</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $report->work_day_type }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $report->start_time }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $report->end_time }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-1">
                                    @if($report->is_overnight)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Overnight
                                        </span>
                                    @endif
                                    @if($report->is_overtime)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                            Overtime
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if(auth()->user()->hasRole('admin'))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Karyawan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $report->user->name }}</dd>
                            </div>
                            @endif
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

                    <!-- Back Button -->
                    <div class="flex justify-end">
                        <a href="{{ route('reports.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 