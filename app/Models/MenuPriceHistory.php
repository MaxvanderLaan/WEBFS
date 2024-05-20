<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'changed_at',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    protected $casts = [
        'price' => 'decimal:2',
        'changed_at' => 'datetime',
    ];
}