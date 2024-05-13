<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasMedia
{

    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'about_me',
        'phone_number',
        'is_admin',
        'account_type',
        'facebook_id',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favouritesAds()
    {
        return $this->hasMany(FavouriteAd::class);
    }


    public function getSlugAttribute(): string
    {
        $slug = urlencode(Str::lower(str_replace(' ', '-', $this->name)));

        return $slug;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function getProfileImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('profile_images');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    /**
     * Get the verification status of the user.
     *
     * @return bool
     */
    public function getVerifiedAttribute(): bool
    {
        $verification = $this->verificationCenter()->first();
        return $verification ? $verification->status === 'verified' : false;
    }

    /**
     * Get the user's verification center data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function verificationCenter()
    {
        return $this->hasOne(VerificationCenter::class);
    }

    public function mobileVerificationCode()
    {
        return $this->hasMany(MobileVerificationCode::class);
    }

}
