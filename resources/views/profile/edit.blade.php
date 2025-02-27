<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <!-- Profile Picture Section -->
                    <section class="mb-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profile Picture') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Update your profile picture.') }}
                            </p>
                        </header>

                        <div class="mt-4">
                            <img src="{{ auth()->user()->avatar_url }}" 
                                alt="{{ auth()->user()->name }}" 
                                class="w-32 h-32 rounded-full object-cover mb-4">
                        </div>
                    </section>

                    <!-- Signature Section -->
                    <section class="mb-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Signature') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Upload your signature image.') }}
                            </p>
                        </header>

                        <div class="mt-4">
                            @if(auth()->user()->signature_url)
                                <img src="{{ auth()->user()->signature_url }}" 
                                    alt="Signature" 
                                    class="max-w-[200px] mb-4">
                            @endif
                        </div>
                    </section>

                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
