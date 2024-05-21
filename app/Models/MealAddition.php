<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MealAddition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class);
    }

    protected function casts(): array
    {
        return [
            'name' => 'string',
        ];
    }
}
