<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkerList extends Model
{
    use HasFactory;

    protected $table = 'marker_list'; // Usa 'marker_lists' si en tu BD está en plural

    protected $fillable = [
        'name',
        'owner_user_id',
        'emoji_identifier'
    ];

    // Usuario que creó la lista
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    // Relación N:M con marcadores
    public function markers()
    {
        return $this->belongsToMany(Marker::class, 'marker_list_markers', 'marker_list_id', 'marker_id');
    }
}
