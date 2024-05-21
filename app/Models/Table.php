<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
    ];

    public function tableCustomers(): HasMany
    {
        return $this->hasMany(TableCustomer::class);
    }

    public function tableHelps(): BelongsToMany
    {
        return $this->belongsToMany(TableHelp::class);
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    protected function casts(): array
    {
        return [
            'number' => 'integer',
        ];
    }
}
