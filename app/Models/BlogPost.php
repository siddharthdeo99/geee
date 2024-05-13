<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BlogPost extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['title', 'slug', 'content', 'reading_time', 'published_at'];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function seo()
    {
        return $this->hasOne(BlogSeo::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'post_id');
    }

    public function getImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('blogs');
    }
}
