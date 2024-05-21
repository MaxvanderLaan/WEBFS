<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableCustomerMenuSale extends Model
{
    use HasFactory;

    public function tableCustomer(): BelongsTo
    {
        return $this->belongsTo(TableCustomer::class);
    }

    public function menuSale(): BelongsTo
    {
        return $this->belongsTo(MenuSale::class);
    }
}
