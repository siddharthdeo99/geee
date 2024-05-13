<?php

namespace App\Models;

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'required',
        'multiple',
        'options',
        'name',
        'type',
    ];
    protected $casts = [
        'options' => 'array',
        'type' => FieldType::class,
    ];
}
