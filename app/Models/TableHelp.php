<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableHelp extends Model
{
    use HasFactory;

    protected $fillable = ['table_id', 'help_id'];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function help(): BelongsTo
    {
        return $this->belongsTo(Help::class);
    }
}
