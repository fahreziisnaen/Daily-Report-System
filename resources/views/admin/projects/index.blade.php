<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Project') }}
            </h2>
            <button @click="$dispatch('open-modal', 'create-project')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                {{ __('Buat Project') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Desktop Table View -->
                    <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kode Project
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama Pekerjaan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Customer
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($projects as $project)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $project->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $project->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $project->customer }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $project->status === 'Berjalan' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $project->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="$dispatch('open-modal', 'edit-project-{{ $project->id }}')"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Edit
                                            </button>
                                            <form class="inline-block ml-2" method="POST" 
                                                action="{{ route('admin.projects.destroy', $project) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus project ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4">
                        @foreach($projects as $project)
                            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ $project->code }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $project->name }}
                                        </p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $project->status === 'Berjalan' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $project->status }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Customer:</span> {{ $project->customer }}
                                    </p>
                                </div>
                                <div class="mt-4 flex justify-end space-x-2">
                                    <button @click="$dispatch('open-modal', 'edit-project-{{ $project->id }}')"
                                        class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                        Edit
                                    </button>
                                    <form class="inline-block" method="POST" 
                                        action="{{ route('admin.projects.destroy', $project) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus project ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="px-3 py-1 text-sm text-red-600 hover:text-red-900 dark:text-red-400">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Project Modal -->
    <x-modal name="create-project" :show="false">
        <form method="POST" action="{{ route('admin.projects.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Buat Project Baru') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="code" :value="__('Kode Project')" />
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="name" :value="__('Nama Pekerjaan')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="customer" :value="__('Customer')" />
                    <x-text-input id="customer" name="customer" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        <option value="Berjalan">Berjalan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Simpan') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Edit Project Modals -->
    @foreach($projects as $project)
        <x-modal name="edit-project-{{ $project->id }}" :show="false">
            <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="p-6">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Edit Project') }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="code-{{ $project->id }}" :value="__('Kode Project')" />
                        <x-text-input id="code-{{ $project->id }}" name="code" type="text" 
                            class="mt-1 block w-full" :value="$project->code" required />
                    </div>

                    <div>
                        <x-input-label for="name-{{ $project->id }}" :value="__('Nama Pekerjaan')" />
                        <x-text-input id="name-{{ $project->id }}" name="name" type="text" 
                            class="mt-1 block w-full" :value="$project->name" required />
                    </div>

                    <div>
                        <x-input-label for="customer-{{ $project->id }}" :value="__('Customer')" />
                        <x-text-input id="customer-{{ $project->id }}" name="customer" type="text" 
                            class="mt-1 block w-full" :value="$project->customer" required />
                    </div>

                    <div>
                        <x-input-label for="status-{{ $project->id }}" :value="__('Status')" />
                        <select id="status-{{ $project->id }}" name="status" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="Berjalan" {{ $project->status === 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="Selesai" {{ $project->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        {{ __('Simpan') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout> 