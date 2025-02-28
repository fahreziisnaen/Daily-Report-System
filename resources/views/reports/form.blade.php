    <div class="mt-4">
        <label class="inline-flex items-center">
            <input type="checkbox" 
                name="is_overnight" 
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                {{ isset($report) && $report->is_overnight ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-600">Overnight (Lanjut hari berikutnya)</span>
        </label>
    </div> 