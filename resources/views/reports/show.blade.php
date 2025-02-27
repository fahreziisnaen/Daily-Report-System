<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Laporan') }}
            </h2>
            <div class="flex space-x-2">
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
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $report->report_date->format('d/m/Y') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kode Project</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $report->project_code }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $report->location }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Waktu</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $report->start_time }} - {{ $report->end_time }}
                                    @if($report->is_overnight)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            Lembur
                                        </span>
                                    @endif
                                </dd>
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