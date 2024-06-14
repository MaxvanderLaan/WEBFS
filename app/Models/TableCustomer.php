<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'deluxe',
        'customer_id',
        'table_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function tableCustomerMenuSales(): BelongsToMany
    {
        return $this->belongsToMany(TableCustomerMenuSale::class);
    }

    protected function casts(): array
    {
        return [
            'deluxe' => 'boolean',
        ];
    }
}
