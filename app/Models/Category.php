<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model  implements Sitemapable, HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'icon',
        'order',
        'slug',
        'description'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function toSitemapTag(): Url | string | array
    {
        // Main category URL
        $urls[] = Url::create(url('categories', [$this->slug]));

        // If this category has subcategories, add them to the URLs list
        foreach ($this->subcategories as $subcategory) {
            $urls[] = Url::create(url('categories', [$this->slug, $subcategory->slug]));
        }

        return $urls;
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public static function getPopularCategories()
    {
        return self::withCount('ads')
                    ->orderBy('ads_count', 'desc')
                    ->take(5)
                    ->get();
    }

    public function getIconAttribute(): ?string
    {
        return $this->getFirstMediaUrl('category_icons');
    }

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'category_fields');
    }
}
