<x-auth-layout>
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <x-brand />

        <a href="/" class="hover:underline text-lg flex items-center gap-x-2" wire:navigate>
            <span> <x-heroicon-o-arrow-left class="w-5 h-5 cursor-pointer" /></span>
            {{ __('messages.t_back_to_home') }}
        </a>
    </div>


    <!-- Login Title -->
    <h1 class="text-2xl md:text-3xl mt-12 mb-8 font-semibold">{{ __('messages.t_login') }}</h1>

    @if($authSettings->enable_google_login)
       <x-social-links />
    @endif
    <!-- Session Status -->
    <x-auth-session-status
        class="rounded-xl bg-green-50 p-4 mb-4 border-green-600"
        :status="session('status')"
    />

    <!-- Validation Errors -->
    <x-auth-validation-errors
        class=" bg-red-50 p-4 mb-4 border rounded-xl border-red-600"
        :errors="$errors"
    />

    <form
        action="{{ route('login') }}"
        method="POST"
        class="space-y-6"
    >
        @csrf
        <div class="space-y-1">
            <x-label
                for="email"
                :value="__('messages.t_email')"
            />
            <div class="mt-1">
                <x-filament::input.wrapper>
                    <x-filament::input
                            id="email"
                            type="text"
                            name="email"
                            autocomplete="email"
                            required
                            autofocus
                    />
                </x-filament::input.wrapper>
            </div>

        </div>

        <div class="space-y-1">
            <x-label
                for="password"
                :value="__('messages.t_password')"
            />
            <div class="mt-1">
                <x-filament::input.wrapper>
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

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <x-checkbox
                    id="remember-me"
                    name="remember-me"
                    :value="__('messages.t_remember_me')"
                />
                <x-label
                    for="remember-me"
                    :value="__('messages.t_remember_me')"
                    class="ml-2"
                />
            </div>

            @if (Route::has('password.request'))
                <div class="text-sm">
                    <a
                        href="{{ route('password.request') }}"
                        class="font-medium  underline "
                        tabindex="-1"
                    >
                        {{ __('messages.t_forgot_password') }}
                    </a>
                </div>
            @endif
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
                    id="login_id"
                    style="display: none;"
                ></div>
                {!! GoogleReCaptchaV3::render(['login_id' => 'login']) !!}
            </div>
        @endif
        <div>
            <x-button.secondary size="lg" class="block w-full dark:bg-primary-600 ">
                {{ __('messages.t_sign_in_action') }}
            </x-button.secondary>
        </div>

        <div>
            <p class="text-sm text-center text-slate-600 dark:text-gray-200">
                {{ __('messages.t_no_account_prompt') }}
                <a
                    href="{{ route('register') }}"
                    class="font-medium underline "
                >
                    {{ __('messages.t_sign_up_action') }}
                </a>
            </p>
        </div>
    </form>
</x-auth-layout>
