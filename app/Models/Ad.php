<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AdStatus;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model  implements Sitemapable, HasMedia
{

    use HasFactory;
    use InteractsWithMedia;
    use HasUuids;
    use SoftDeletes;

    protected $casts = [
        'status' => AdStatus::class,
        'tags' => 'array',
    ];

    protected $fillable = [
        'title',
        'description',
        'price',
        'price_type_id',
        'condition_id',
        'posted_date',
        'user_id',
        'slug',
        'category_id',
        'for_sale_by',
        'city',
        'postal_code',
        'state',
        'country',
        'latitude',
        'longitude',
        'location_name',
        'tags',
        'type',
        'video_link',
        'phone_number',
        'display_phone',
        'status',
        'website_url'
    ];


    public function toSitemapTag(): Url | string | array
    {
        return url('ad', $this->slug);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getPrimaryImageAttribute(): ?string
    {
        $imageUrl = $this->getFirstMediaUrl('ads', 'thumb');
        // Check if the image URL is not empty
        if (!empty($imageUrl)) {
            return $imageUrl;
        }
        return null;
    }

    public function getOgImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('ads');
    }

    public function images(): array
    {
        return $this->getMedia('ads')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'ad_promotions')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->crop('crop-center', 200, 200);
    }

    public function priceType()
    {
        return $this->belongsTo(PriceType::class);
    }

    public function condition()
    {
        return $this->belongsTo(AdCondition::class);
    }

    public function usedPackageItems()
    {
        return $this->hasMany(UsedPackageItem::class);
    }

}
