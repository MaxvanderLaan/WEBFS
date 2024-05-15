<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function MenuSales(): HasMany
    {
        return $this->hasMany(MenuSale::class);
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
