<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
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
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
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
                                <div x-data="{ 
                                    showConfirmation: false,
                                    currentRole: '{{ $user->roles->first()->name }}',
                                    newRole: '{{ $user->roles->first()->name }}',
                                    formSubmitted: false,
                                    confirmationMessage: '',
                                    
                                    updateRole() {
                                        if (this.formSubmitted) return;
                                        
                                        if (this.newRole === 'Super Admin' && this.currentRole !== 'Super Admin') {
                                            this.confirmationMessage = 'Apakah Anda yakin ingin mengubah role user ini menjadi Super Admin?';
                                            this.showConfirmation = true;
                                        } else if (this.currentRole === 'Super Admin' && this.newRole !== 'Super Admin') {
                                            this.confirmationMessage = 'Peringatan: Mengubah role Super Admin menjadi Employee akan menghilangkan semua hak akses admin. Lanjutkan?';
                                            this.showConfirmation = true;
                                        } else {
                                            this.submitRoleUpdate();
                                        }
                                    },
                                    
                                    submitRoleUpdate() {
                                        this.formSubmitted = true;
                                        this.$refs.roleForm.submit();
                                    }
                                }">
                                    <form x-ref="roleForm" 
                                        action="{{ route('admin.users.update-role', $user) }}" 
                                        method="POST" 
                                        class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" 
                                            x-model="newRole"
                                            @change="updateRole()"
                                            class="rounded-md border-gray-300 text-sm">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" 
                                                    {{ $user->roles->first()->name === $role->name ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <!-- Modal Konfirmasi -->
                                    <div x-show="showConfirmation" 
                                        class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 flex items-center justify-center"
                                        x-cloak>
                                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                                            <div class="p-4 border-b">
                                                <h3 class="text-lg font-medium text-gray-900">
                                                    Konfirmasi Update Role
                                                </h3>
                                            </div>

                                            <div class="p-4">
                                                <div class="mb-4 text-sm text-gray-600">
                                                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                                    <p class="mt-2"><strong>Perubahan Role:</strong></p>
                                                    <p class="text-yellow-600">
                                                        <span x-text="currentRole"></span> → 
                                                        <span x-text="newRole"></span>
                                                    </p>
                                                </div>
                                                <p class="text-gray-600" x-text="confirmationMessage"></p>
                                            </div>

                                            <div class="p-4 border-t flex justify-end space-x-3">
                                                <button type="button" 
                                                    @click="showConfirmation = false; newRole = currentRole"
                                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                                                    Batal
                                                </button>
                                                <button type="button"
                                                    @click="submitRoleUpdate()"
                                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md text-sm font-medium hover:bg-yellow-700">
                                                    Konfirmasi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-sm rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                        class="text-blue-600 hover:text-blue-900"
                                        title="Edit User">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <button type="button"
                                        @click="$dispatch('open-modal', 'reset-password-{{$user->id}}')"
                                        class="text-yellow-600 hover:text-yellow-900"
                                        title="Reset Password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <!-- Toggle Active Button -->
                                        <button 
                                            @click="$dispatch('open-modal', 'toggle-active-{{$user->id}}')"
                                            class="text-orange-600 hover:text-orange-900"
                                            title="{{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="{{ $user->is_active 
                                                        ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' 
                                                        : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button 
                                            @click="$dispatch('open-modal', 'delete-user-{{$user->id}}')"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete User">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
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
    <x-modal name="add-user" :show="session()->has('show-add-user-modal')">
        <form method="POST" 
            action="{{ route('admin.users.store') }}" 
            x-data="{ 
                showConfirmation: false,
                selectedRole: '{{ old('role', 'employee') }}',
                formSubmitted: false,
                
                submitForm(e) {
                    e.preventDefault();
                    
                    if (this.formSubmitted) return;
                    
                    if (this.selectedRole === 'admin') {
                        this.showConfirmation = true;
                    } else {
                        this.formSubmitted = true;
                        $refs.addUserForm.submit();
                    }
                },
                
                confirmSubmit() {
                    if (this.formSubmitted) return;
                    
                    this.formSubmitted = true;
                    $refs.addUserForm.submit();
                }
            }" 
            x-ref="addUserForm"
            class="p-6">
            @csrf

            <!-- Error Dialog -->
            @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Terdapat beberapa kesalahan:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('Add New User') }}
            </h2>

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" 
                    name="name" 
                    type="text" 
                    class="mt-1 block w-full" 
                    :value="old('name')"
                    required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" 
                    name="email" 
                    type="email" 
                    class="mt-1 block w-full" 
                    :value="old('email')"
                    required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Homebase -->
            <div class="mb-4">
                <x-input-label for="homebase" :value="__('Homebase')" />
                <x-text-input id="homebase" 
                    name="homebase" 
                    type="text" 
                    class="mt-1 block w-full" 
                    :value="old('homebase')"
                    required />
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

            <!-- Modal Konfirmasi Admin -->
            <div x-show="showConfirmation" 
                class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 flex items-center justify-center"
                @click.away="showConfirmation = false; selectedRole = 'employee'">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" @click.stop>
                    <div class="p-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Konfirmasi Role Admin</h3>
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600">Apakah Anda yakin ingin menambahkan user ini sebagai Admin?</p>
                    </div>

                    <div class="p-4 border-t flex justify-end space-x-3">
                        <button type="button" 
                            @click="showConfirmation = false; selectedRole = 'employee'"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="button"
                            @click="confirmSubmit()"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md text-sm font-medium hover:bg-yellow-700">
                            Konfirmasi
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" @click="submitForm($event)">
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

            <h2 class="text-lg font-medium text-gray-900">
                Reset Password User
            </h2>

            <div class="mt-2 text-sm text-gray-600">
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
            </div>

            <!-- New Password -->
            <div class="mt-4">
                <x-input-label for="new_password_{{$user->id}}" value="Password Baru" />
                <x-text-input id="new_password_{{$user->id}}" 
                    name="new_password" 
                    type="password" 
                    class="mt-1 block w-full" 
                    required />
                <x-input-error :messages="$errors->get('new_password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="new_password_confirmation_{{$user->id}}" value="Konfirmasi Password Baru" />
                <x-text-input id="new_password_confirmation_{{$user->id}}" 
                    name="new_password_confirmation" 
                    type="password" 
                    class="mt-1 block w-full" 
                    required />
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Password baru akan langsung aktif setelah disimpan. User perlu menggunakan password baru untuk login berikutnya.
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    Reset Password
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Toggle Active Modal -->
    <x-modal name="toggle-active-{{$user->id}}" focusable>
        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <h2 class="text-lg font-medium text-gray-900">
                {{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}
            </h2>

            <div class="mt-2 text-sm text-gray-600">
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
            </div>

            @if(!$user->is_active)
                <p class="mt-4 text-sm text-gray-600">
                    Apakah Anda yakin ingin mengaktifkan kembali akun user ini?
                </p>
            @else
                <div class="mt-4">
                    <x-input-label for="reason" value="Alasan penonaktifan" />
                    <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full" required />
                    <p class="mt-2 text-sm text-red-500">
                        Perhatian: User yang dinonaktifkan tidak akan dapat mengakses sistem. 
                        Semua data dan laporan tetap tersimpan namun tidak dapat diakses sampai akun diaktifkan kembali.
                    </p>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal name="delete-user-{{$user->id}}" focusable>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="p-6">
            @csrf
            @method('DELETE')
            
            <h2 class="text-lg font-medium text-gray-900">
                Hapus Akun User
            </h2>

            <div class="mt-2 text-sm text-gray-600">
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
            </div>

            <p class="mt-4 text-sm text-red-600 font-medium">
                Peringatan: Tindakan ini tidak dapat dibatalkan!
            </p>

            <p class="mt-2 text-sm text-gray-600">
                Semua data berikut akan dihapus secara permanen:
            </p>

            <ul class="list-disc ml-4 mt-2 text-sm text-gray-600">
                <li>Akun dan profil user</li>
                <li>Seluruh laporan yang dibuat oleh user</li>
                <li>File yang diunggah (avatar, tanda tangan)</li>
            </ul>

            <div class="mt-6">
                <x-input-label for="confirm" value="Ketik DELETE untuk konfirmasi" />
                <x-text-input 
                    id="confirm" 
                    type="text" 
                    class="mt-1 block w-full"
                    x-data=""
                    x-on:input="$el.form.querySelector('button[type=submit]').disabled = $el.value !== 'DELETE'"
                    required />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-primary-button class="ml-3 bg-red-600 hover:bg-red-700" disabled>
                    Hapus User
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    @endforeach
</x-app-layout>