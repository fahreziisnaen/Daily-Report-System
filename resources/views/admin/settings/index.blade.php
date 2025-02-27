@php
use App\Models\Settings;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- LDAP Authentication Toggle -->
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="ldap_enabled" 
                                       id="ldap_enabled"
                                       value="1" 
                                       {{ $ldapEnabled ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-gray-700">Enable LDAP Authentication</span>
                            </label>
                        </div>

                        <!-- LDAP Settings -->
                        <div id="ldap-settings" class="mb-4 {{ $ldapEnabled ? '' : 'hidden' }}">
                            <h3 class="font-bold mb-2">LDAP Settings</h3>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    LDAP Host
                                </label>
                                <input type="text" name="ldap_host" value="{{ $ldapHost }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    LDAP Port
                                </label>
                                <input type="number" name="ldap_port" value="{{ $ldapPort }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    LDAP Base DN
                                </label>
                                <input type="text" name="ldap_base_dn" value="{{ $ldapBaseDn }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    LDAP Username
                                </label>
                                <input type="text" name="ldap_username" value="{{ $ldapUsername }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    LDAP Password
                                </label>
                                <input type="password" name="ldap_password" value="{{ $ldapPassword }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>

                            <button type="button" id="test-connection" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Test Connection
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ldapEnabled = document.querySelector('input[name="ldap_enabled"]');
            const ldapSettings = document.querySelector('#ldap-settings');

            // Function untuk toggle visibility
            function toggleLdapSettings() {
                console.log('Checkbox changed:', ldapEnabled.checked); // Debug
                if (ldapEnabled.checked) {
                    ldapSettings.classList.remove('hidden');
                } else {
                    ldapSettings.classList.add('hidden');
                }
            }

            // Add event listener
            ldapEnabled.addEventListener('change', toggleLdapSettings);

            // Initial state
            toggleLdapSettings();

            // Test connection handler
            const testButton = document.querySelector('#test-connection');
            testButton?.addEventListener('click', async function() {
                try {
                    const response = await fetch('/admin/settings/test-ldap', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ldap_host: document.querySelector('input[name="ldap_host"]').value,
                            ldap_port: document.querySelector('input[name="ldap_port"]').value,
                            ldap_username: document.querySelector('input[name="ldap_username"]').value,
                            ldap_password: document.querySelector('input[name="ldap_password"]').value
                        })
                    });

                    const result = await response.json();
                    alert(result.message);
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to test connection');
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 