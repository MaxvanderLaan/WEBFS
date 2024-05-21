<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(MenuPriceHistory::class);
    }

    public function menuOffers(): HasMany
    {
        return $this->hasMany(MenuOffer::class);
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
