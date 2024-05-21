<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MenuSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'remark',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function mealAddition(): HasOne 
    {
        return $this->hasOne(MealAddition::class);
    }

    public function tableCustomerMenuSales(): BelongsToMany
    {
        return $this->belongsToMany(TableCustomerMenuSale::class);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'remark' => 'string',
        ];
    }
}
