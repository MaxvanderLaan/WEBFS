<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'table_id',
    ];

    public function menuSales(): HasMany
    {
        return $this->hasMany(MenuSale::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    protected function casts(): array
    {
        return [
            'description' => 'string',
            'table_id' => 'integer',
        ];
    }
}
