<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'string',
        ];
    }
}
