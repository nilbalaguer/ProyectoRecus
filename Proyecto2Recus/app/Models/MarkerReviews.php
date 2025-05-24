<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkerReviews extends Model
{
    use HasFactory;

    protected $table = 'marker_reviews';

    protected $fillable = [
        'review_stars',
        'review_content',
        'user_id',
        'marker_id',
    ];
    
}
