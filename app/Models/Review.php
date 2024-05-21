<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
    ];

    protected function casts(): array
    {
        return [
            'message' => 'string',
        ];
    }
}
