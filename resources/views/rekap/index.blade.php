<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Rekap') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <form method="GET" action="{{ route('admin.rekap.index') }}" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <x-input-label for="month" :value="__('Bulan')" />
                            <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300" onchange="this.form.submit()">
                                @foreach($months as $value => $label)
                                    <option value="{{ $value }}" {{ $value == $month ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <x-input-label for="year" :value="__('Tahun')" />
                            <select name="year" id="year" class="mt-1 block w-full rounded-md border-gray-300" onchange="this.form.submit()">
                                @foreach($years as $yearOption)
                                    <option value="{{ $yearOption }}" {{ $yearOption == $year ? 'selected' : '' }}>
                                        {{ $yearOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Desktop Table View (Hidden on Mobile) -->
            <div class="hidden md:block">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Jam Kerja
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Jam Lembur
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah Laporan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user['name'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ \App\Helpers\TimeHelper::formatHoursToHoursMinutes($user['total_work_hours']) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ \App\Helpers\TimeHelper::formatHoursToHoursMinutes($user['total_overtime_hours']) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user['report_count'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('admin.rekap.export', ['user' => $user['id'], 'month' => $month, 'year' => $year]) }}" 
                                                class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-md hover:bg-green-100">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Export Excel
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mobile Card View (Hidden on Desktop) -->
            <div class="md:hidden space-y-4">
                @foreach($users as $user)
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="border-b pb-3 mb-3">
                            <h3 class="text-lg font-medium text-gray-900">{{ $user['name'] }}</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Total Jam Kerja</span>
                                <span class="text-sm font-medium text-gray-900">{{ \App\Helpers\TimeHelper::formatHoursToHoursMinutes($user['total_work_hours']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Total Jam Lembur</span>
                                <span class="text-sm font-medium text-gray-900">{{ \App\Helpers\TimeHelper::formatHoursToHoursMinutes($user['total_overtime_hours']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Jumlah Laporan</span>
                                <span class="text-sm font-medium text-gray-900">{{ $user['report_count'] }}</span>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('admin.rekap.export', ['user' => $user['id'], 'month' => $month, 'year' => $year]) }}" 
                                class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-md hover:bg-green-100">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export Excel
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout> 