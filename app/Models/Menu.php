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
        'is_archived',
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
            'is_archived' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::updated(function ($menu) {
            if ($menu->wasChanged('price')) {
                $history = new MenuPriceHistory;
                $history->menu_id = $menu->id;
                $history->price = $menu->price;
                $history->changed_at = now();
                $history->save();
            }
        });
    }
}
