<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'promotion_id',
        'start_date',
        'end_date',
        'price'
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'start_date' => 'datetime',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
