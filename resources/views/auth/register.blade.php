<x-auth-layout>
    <style>
        .iti {
            width: 100%;
        }
    </style>
    {{-- @assets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    {{-- @endassets --}}
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <x-brand />
        <a href="/" class="hover:underline text-lg flex items-center gap-x-2" wire:navigate>
            <span> <x-heroicon-o-arrow-left class="w-5 h-5 cursor-pointer" /></span>
            {{ __('messages.t_back_to_home') }}
        </a>
    </div>

    <!-- Register Title -->
    <h1 class="text-2xl md:text-3xl mt-12 mb-8 font-semibold">{{ __('messages.t_create_account_prompt', ['siteName' => $generalSettings->site_name]) }}</h1>


    @if($authSettings->enable_google_login)
        <x-social-links />
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="rounded-xl bg-green-50 p-4 mb-4" :status="session('status')" />

    <!-- Validation Errors -->
    <x-auth-validation-errors class="rounded-xl bg-red-50 p-4 mb-4 border  border-red-600" :errors="$errors" />

    <div class="mt-2">
        <form action="{{ route('register') }}" method="POST" class="space-y-6">
            @csrf
            <!-- Name -->
            <div>
                <x-label for="name" :value="__('messages.t_name')" />

                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input
                            id="name"
                            type="text"
                            name="name"
                            :value="old('name')"
                            required
                            autofocus
                        />
                    </x-filament::input.wrapper>
            </div>
            <!-- Email -->
            <div>
                <x-label for="email" :value="__('messages.t_email')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input
                            id="email"
                            type="text"
                            name="email"
                            :value="old('name')"
                            autocomplete="email"
                            required
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>

            @if($loginOtpSettings->enabled)
            <!-- Phone Number -->
            <div>
                <x-label for="phone_number" :value="__('messages.t_phone_number')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1" wire:ignore>
                        <x-filament::input id="phone_number" type="tel" name="phone_number" required
                            onblur="getPhoneNumber(event)" />
                        <input type="text" id='phone' name="phone" hidden>
                    </x-filament::input.wrapper>
                    <div class="alert alert-info text-red-500" style="display: none;"></div>

                </div>
            </div>
            @endif
            <!-- Password -->
            <div>
                <x-label for="password" :value="__('messages.t_password')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>
            <!-- Confirm Password -->
            <div>
                <x-label for="password_confirmation" :value="__('messages.t_confirm_password')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input
                            id="password_confirmation"
                            class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation"
                            required
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>
            <!-- Captcha -->
            @if($authSettings->recaptcha_enabled)
            <div>
                <div class="bg-slate-100 p-4 rounded-md text-sm text-slate-600">
                    This site is protected by reCAPTCHA and the Google
                    <a
                        href="https://policies.google.com/privacy"
                        class="hover:text-slate-500"
                        tabindex="-1"
                    >Privacy Policy</a> and
                    <a
                        href="https://policies.google.com/terms"
                        class="hover:text-slate-500"
                        tabindex="-1"
                    >Terms of Service</a> apply.
                </div>
                <div
                    id="signup_id"
                    style="display: none;"
                ></div>
                {!! GoogleReCaptchaV3::render(['signup_id' => 'register']) !!}
            </div>
            @endif
            <div>
                <x-button.secondary size="lg" class="block w-full dark:bg-primary-600">
                    {{ __('messages.t_create_account_button') }}
                </x-button.secondary>
            </div>
            <div>
                <p class="text-sm text-center text-slate-600 dark:text-gray-200">
                    {{ __('messages.t_already_have_account') }}
                    <a href="{{ route('login') }}" class="font-medium  underline">
                        {{ __('messages.t_login') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
    <script>
        const phoneInputField = document.querySelector("#phone_number");

        const phoneInput = window.intlTelInput(phoneInputField, {
            initialCountry: "us",
            utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
                const info = document.querySelector(".alert-info");
            function getPhoneNumber(event) {
            event.preventDefault();
            const phoneNumber = phoneInput.getNumber();
            document.getElementById ("phone").value=phoneNumber;
            if (!phoneInput.isValidNumber() && phoneNumber.length>0) {
            info.style.display = "";
            info.innerHTML = `Phone number is not valid`;
            }else{
                info.innerHTML=''
            }
            }
    </script>
</x-auth-layout>
