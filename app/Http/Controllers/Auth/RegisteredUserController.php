<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use TimeHunter\LaravelGoogleReCaptchaV3\Validations\GoogleReCaptchaV3ValidationRule;
use Adfox\LoginOtp\Livewire\SendOtp;
use App\Settings\LoginOtpSettings;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $loginOtpSettings = app(LoginOtpSettings::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => [new GoogleReCaptchaV3ValidationRule('register')]
        ]);

        $data = $loginOtpSettings->enabled ?
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone,
                'password' => Hash::make($request->password),
            ] :
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];

        $user = User::create($data);

        event(new Registered($user));

        Auth::login($user);

        if ($loginOtpSettings->enabled) {
            $sendOtp = new SendOtp;
            //call send otp method
            return $sendOtp->sendOtp($user->phone_number);
        } else {
            Auth::login($user);
            return redirect(RouteServiceProvider::HOME);
        }
    }
}
