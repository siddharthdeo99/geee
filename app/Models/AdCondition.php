<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCondition extends Model
{
    use HasFactory;
    
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}
