<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'discount',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'menu_id' => 'integer',
        'discount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function getPriceAttribute()
    {
        $priceHistory = $this->menu->priceHistories()
            ->where('changed_at', '>=', $this->start_date)
            ->where('changed_at', '<=', $this->end_date)
            ->orderBy('changed_at', 'desc')
            ->first();

        return $priceHistory ? $priceHistory->price : $this->menu->price;
    }
}