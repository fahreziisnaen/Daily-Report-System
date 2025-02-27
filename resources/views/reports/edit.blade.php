<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form x-data="reportForm" method="POST" action="{{ route('reports.update', $report) }}" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-6">Informasi Pekerjaan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="report_date" class="block text-sm font-medium text-gray-700">Tanggal</label>
                                    <input type="date" name="report_date" id="report_date" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        required value="{{ old('report_date', $report->report_date->format('Y-m-d')) }}">
                                    @error('report_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="project_code" class="block text-sm font-medium text-gray-700">Kode Project</label>
                                    <input type="text" name="project_code" id="project_code" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        required value="{{ old('project_code', $report->project_code) }}">
                                    @error('project_code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700">Lokasi</label>
                                    <input type="text" name="location" id="location" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        required value="{{ old('location', $report->location) }}">
                                    @error('location')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                                        <input type="time" name="start_time" id="start_time" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                            required value="{{ old('start_time', $report->start_time) }}">
                                        @error('start_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="end_time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                                        <input type="time" name="end_time" id="end_time" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                            required value="{{ old('end_time', $report->end_time) }}">
                                        @error('end_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-span-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_overnight" 
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            {{ old('is_overnight', $report->is_overnight) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600">
                                            Lembur sampai esok hari
                                            <span class="text-xs text-gray-500">(Centang jika waktu selesai melewati tengah malam)</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Work Details Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-6">Detail Pekerjaan</h3>
                            <div class="space-y-4" id="work-details-container">
                                <template x-for="(detail, index) in workDetails" :key="index">
                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-sm font-medium text-gray-900">Detail #<span x-text="index + 1"></span></h4>
                                            <button type="button" @click="removeDetail(index)"
                                                class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div class="md:col-span-3">
                                                <label :for="'description_' + index" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                                <textarea :name="'work_details[' + index + '][description]'" :id="'description_' + index" rows="3" 
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                    required x-model="detail.description"></textarea>
                                            </div>
                                            <div>
                                                <label :for="'status_' + index" class="block text-sm font-medium text-gray-700">Status</label>
                                                <select :name="'work_details[' + index + '][status]'" :id="'status_' + index" 
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    x-model="detail.status">
                                                    <option value="Selesai">Selesai</option>
                                                    <option value="Dalam Proses">Dalam Proses</option>
                                                    <option value="Tertunda">Tertunda</option>
                                                    <option value="Bermasalah">Bermasalah</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Tombol Tambah Detail -->
                                <div class="flex justify-center pt-4">
                                    <button type="button" @click="addDetail" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        {{ __('Tambah Detail') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('reports.show', $report) }}" 
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                                {{ __('Batal') }}
                            </a>
                            
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportForm', () => ({
                workDetails: {!! json_encode($report->details->map(function($detail) {
                    return [
                        'description' => $detail->description,
                        'status' => $detail->status
                    ];
                })->toArray()) !!} || [{
                    description: '',
                    status: 'Selesai'
                }],

                addDetail() {
                    this.workDetails.push({
                        description: '',
                        status: 'Selesai'
                    });
                },

                removeDetail(index) {
                    if (this.workDetails.length > 1) {
                        this.workDetails.splice(index, 1);
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout> 