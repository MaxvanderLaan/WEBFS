<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningTable extends Model
{
    use HasFactory;

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}