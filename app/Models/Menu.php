<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'addition',
        'name',
        'price',
        'description',
    ];

    public function menuSales(): HasMany
    {
        return $this->hasMany(MenuSale::class);
    }

    public function mealTypes(): HasOne
    {
        return $this->hasOne(MealType::class);
    }

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'addition' => 'string',
            'name' => 'string',
            'price' => 'decimal:2',
            'description' => 'string',
        ];
    }
}
