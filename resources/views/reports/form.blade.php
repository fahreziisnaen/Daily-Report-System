<div class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pekerjaan</h3>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="report_date" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    required>
            </div>

            <!-- Project Code with Autocomplete -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Kode Project</label>
                <div class="relative">
                    <input type="text" name="project_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        x-data="projectAutocomplete()"
                        x-init="init()"
                        x-on:input.debounce="fetchProjects"
                        required>
                    <!-- Autocomplete results -->
                </div>
            </div>

            <!-- Other fields... -->
        </div>
    </div>

    <!-- Work Details -->
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Pekerjaan</h3>
        <div x-data="workDetails()" class="space-y-4">
            <template x-for="(detail, index) in workDetails" :key="index">
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg">
                    <!-- Work detail fields... -->
                </div>
            </template>
        </div>
    </div>
</div>

<div class="mt-4">
    <label class="inline-flex items-center">
        <input type="checkbox" 
            name="is_overnight" 
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
            {{ isset($report) && $report->is_overnight ? 'checked' : '' }}>
        <span class="ml-2 text-sm text-gray-600">Overnight (Lanjut hari berikutnya)</span>
    </label>
</div> 