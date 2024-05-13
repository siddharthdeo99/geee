<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class SocialiteController extends Controller
{
    public function loginSocial(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($request);

        return Socialite::driver($provider)->redirect();
    }

    public function callbackSocial(Request $request, string $provider)
    {
        $this->validateProvider($request);

        $response = Socialite::driver($provider)->user();

        // Check if a user with this email already exists
        $user = User::where('email', $response->getEmail())->first();

        if (!$user) {
            // Create a new user if it doesn't exist
            $user = new User();
            $user->email = $response->getEmail();
            $user->password = Str::random(10); // Generate a random password
            $user->name = $response->getName() ?? explode('@', $response->getEmail())[0];
            $user->email_verified_at = now(); // Mark the email as verified
            $user->save();

            // Associate the user with the social provider
            $user->{$provider . '_id'} = $response->getId();
            $user->save();

            // Fire the Registered event
            event(new Registered($user));
        } else {
            // If user exists, update the provider id
            $user->update([$provider . '_id' => $response->getId()]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }


    protected function validateProvider(Request $request): array
    {
        return $this->getValidationFactory()->make(
            $request->route()->parameters(),
            ['provider' => 'in:facebook,google']
        )->validate();
    }
}
