<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" 
        action="{{ request()->routeIs('admin.users.edit') 
            ? route('admin.users.update', $user) 
            : route('profile.update') }}" 
        class="mt-6 space-y-6" 
        enctype="multipart/form-data">
        @csrf
        @method(request()->routeIs('admin.users.edit') ? 'put' : 'patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            @if(request()->routeIs('admin.users.edit'))
                <!-- Saat admin edit user, email field disabled -->
                <x-text-input id="email" type="email" class="mt-1 block w-full bg-gray-100" :value="$user->email" disabled readonly />
            @else
                <!-- Saat user edit profile sendiri -->
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        <div>
            <x-input-label for="homebase" :value="__('Homebase')" />
            <x-text-input id="homebase" name="homebase" type="text" class="mt-1 block w-full" :value="old('homebase', $user->homebase)" required />
            <x-input-error class="mt-2" :messages="$errors->get('homebase')" />
        </div>

        <!-- Profile Picture Input -->
        <div>
            <x-input-label for="avatar" :value="__('Profile Picture')" />
            <input type="file" 
                id="avatar" 
                name="avatar" 
                accept="image/*"
                class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <!-- Signature Input -->
        <div>
            <x-input-label for="signature" :value="__('Signature')" />
            <input type="file" 
                id="signature" 
                name="signature" 
                accept="image/*"
                class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100" />
            <x-input-error class="mt-2" :messages="$errors->get('signature')" />
        </div>

        <div class="flex items-center gap-4">
            @if(request()->routeIs('admin.users.edit'))
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Batal') }}
                </a>
            @endif
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
