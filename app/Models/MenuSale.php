<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MenuSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
    ];

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
        ];
    }
}
