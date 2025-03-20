<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Url extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['original_url', 'short_url', 'expires_at'];

   // Cast `expires_at` as a datetime
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
