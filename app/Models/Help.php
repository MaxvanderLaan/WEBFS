<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Help extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'completed',
    ];

    public function tableHelps(): BelongsToMany
    {
        return $this->belongsToMany(TableHelp::class);
    }

    protected function casts(): array
    {
        return [
            'message' => 'string',
            'completed' => 'boolean',
        ];
    }
}
