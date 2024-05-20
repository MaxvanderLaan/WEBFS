<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birthday',
    ];

    public function tableCustomers(): HasMany
    {
        return $this->hasMany(TableCustomer::class);
    }

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'birthday' => 'date',
        ];
    }
}
