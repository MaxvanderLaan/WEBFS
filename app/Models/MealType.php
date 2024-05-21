<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    protected function casts(): array
    {
        return [
            'type' => 'string',
        ];
    }
}
