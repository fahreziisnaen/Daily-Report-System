<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Management') }}
            </h2>
            <button 
                type="button"
                x-data=""
                @click="$dispatch('open-modal', 'add-user')" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add User
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search/Filter Section -->
            <div class="mb-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name, email or homebase..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="w-full sm:w-auto">
                        <select name="role" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:w-auto flex gap-2">
                        <button type="submit" 
                            class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Search
                        </button>
                        @if(request()->hasAny(['search', 'role']))
                            <a href="{{ route('admin.users.index') }}" 
                                class="flex-1 sm:flex-none px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Table - Responsive -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name/Email</th>
                            <th class="hidden md:table-cell px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Homebase</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500 md:hidden">{{ $user->email }}</div>
                                <div class="text-sm text-gray-500 md:hidden">{{ $user->homebase ?: '-' }}</div>
                            </td>
                            <td class="hidden md:table-cell px-3 py-2 text-sm">{{ $user->homebase ?: '-' }}</td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" onchange="this.form.submit()" 
                                        class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', 'reset-password-{{$user->id}}')"
                                        class="text-yellow-600 hover:text-yellow-900"
                                        title="Reset Password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                onclick="return confirm('Are you sure you want to remove this user?')"
                                                class="text-red-600 hover:text-red-900"
                                                title="Remove User">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <x-modal name="add-user" :show="false">
        <form x-data="{ 
                showConfirmation: false,
                selectedRole: 'employee',
                formData: new FormData(),
                
                submitForm() {
                    if (this.selectedRole === 'admin' && !this.showConfirmation) {
                        this.showConfirmation = true;
                        return;
                    }
                    this.$refs.form.submit();
                }
            }" 
            x-ref="form"
            method="POST" 
            action="{{ route('admin.users.store') }}" 
            class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('Add New User') }}
            </h2>

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Homebase -->
            <div class="mb-4">
                <x-input-label for="homebase" :value="__('Homebase')" />
                <x-text-input id="homebase" name="homebase" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('homebase')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Role -->
            <div class="mb-4">
                <x-input-label for="role" :value="__('Role')" />
                <select id="role" 
                    name="role" 
                    x-model="selectedRole"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $role->name === 'employee' ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Admin Role Confirmation Modal -->
            <template x-teleport="body">
                <div x-show="showConfirmation" 
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 flex items-center justify-center"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    
                    <div x-show="showConfirmation"
                        @click.outside="showConfirmation = false"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                        
                        <!-- Modal Header -->
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-medium text-gray-900 text-left">Konfirmasi Role Admin</h3>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-4">
                            <div class="flex items-start mb-4">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-2 mr-3">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600">Apakah Anda yakin ingin menambahkan user ini sebagai Admin?</p>
                            </div>
                            <div class="text-sm text-gray-500 bg-gray-50 rounded p-3">
                                <div class="text-left">
                                    <p>User dengan role Admin memiliki akses penuh ke semua fitur sistem.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-4 border-t flex justify-end space-x-3">
                            <button type="button" 
                                @click="showConfirmation = false; selectedRole = 'employee'"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                                Batal
                            </button>
                            <button type="button"
                                @click="$refs.form.submit()"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md text-sm font-medium hover:bg-yellow-700">
                                Konfirmasi
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" @click.prevent="submitForm">
                    {{ __('Add User') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Reset Password Modals -->
    @foreach($users as $user)
    <x-modal name="reset-password-{{$user->id}}" :show="false">
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="p-6">
            @csrf
            @method('POST')

            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('Reset Password for ') . $user->name }}
            </h2>

            <!-- New Password -->
            <div class="mb-4">
                <x-input-label for="new_password_{{$user->id}}" :value="__('New Password')" />
                <x-text-input id="new_password_{{$user->id}}" 
                    name="new_password" 
                    type="password" 
                    class="mt-1 block w-full" 
                    required />
                <x-input-error :messages="$errors->get('new_password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <x-input-label for="new_password_confirmation_{{$user->id}}" :value="__('Confirm New Password')" />
                <x-text-input id="new_password_confirmation_{{$user->id}}" 
                    name="new_password_confirmation" 
                    type="password" 
                    class="mt-1 block w-full" 
                    required />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    @endforeach
</x-app-layout>