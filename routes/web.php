<?php
use App\Livewire\Ad\AdDetails;
use App\Livewire\Ad\AdLimitReached;
use App\Livewire\Ad\AdList;
use App\Livewire\Home\Home;
use App\Livewire\Ad\PostAd\PostAd;
use App\Livewire\User\MyAds;
use App\Livewire\User\MyFavorites;
use App\Livewire\User\MyMessages;
use App\Livewire\User\MyProfile;
use App\Livewire\User\ViewProfile;

use App\Livewire\Ad\SuccessAd;
use App\Livewire\Ad\SuccessUpgrade;
use App\Livewire\User\MyAccount;
use App\Livewire\User\PageDetail;
use App\Livewire\User\Contact;
use App\Livewire\User\Verification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('/manifest.json', '\App\Http\Controllers\PwaController@manifest');



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group([], function () {

    Route::get('/', Home::class)->name('home');

    Route::get('/categories/{category}/{subcategory?}', AdList::class)->name('ad-category');

    Route::get('/search', AdList::class)->name('search');

    Route::get('/ad/{slug}', AdDetails::class)->name('ad-details');

    Route::get('/pages/{page:slug}', PageDetail::class)->name('page-details');

    Route::get('/contact', Contact::class)->name('contact');

    Route::get('/profile/{slug}/{id}', ViewProfile::class)->name('view-profile');

    Route::get('/location/{location}/{category}/{subcategory?}', AdList::class)->name('location-category');

});



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/post-ad', PostAd::class)->name('post-ad');

    Route::get('/success-ad/{id}', SuccessAd::class)->name('success-ad');

    Route::get('/success/ad-upgrade', SuccessUpgrade::class)->name('success-upgrade');

    Route::get('/my-account', MyAccount::class)->name('my-account');

    Route::get('/my-ads', MyAds::class)->name('my-ads');

    Route::get('/my-messages', MyMessages::class)->name('my-messages');

    Route::get('/my-profile', MyProfile::class)->name('my-profile');

    Route::get('/my-favorites', MyFavorites::class)->name('my-favorites');

    Route::get('/verification-center', Verification::class)->name('verification');

});


// Callback routes for payment gateways
Route::namespace('App\Http\Controllers\Callback')->prefix('callback')->group(function() {
    // Stripe
    Route::get('stripe', 'StripeController@callback');
});

require __DIR__.'/auth.php';

