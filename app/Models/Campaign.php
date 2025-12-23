<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'location',
        'organization',
        'image_path',
        'target_amount',
        'raised_amount',
        'excerpt',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


