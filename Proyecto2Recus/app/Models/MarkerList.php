<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkerList extends Model
{
    use HasFactory;

    protected $table = 'marker_list';

    protected $fillable = [
        'name',
        'owner_user_id',
        'emoji_identifier'
    ];
}
