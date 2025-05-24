<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkerListMarkers extends Model
{
    use HasFactory;

    protected $table = 'marker_list_markers';

    protected $fillable = [
        'id_friend',
        'friend_group_id',
    ];
}
