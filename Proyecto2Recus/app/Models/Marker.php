<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    use HasFactory;

    protected $table = 'markers';

    protected $fillable = [
        'name',
        'description',
        'lng',
        'lat',
        'zoom',
        'pitch',
        'bearing',
        'marker_list_id',
        'user_id',
    ];

    // Usuario que creo el marcador
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación 1:N con MarkerList
    public function markerList()
    {
        return $this->belongsTo(MarkerList::class, 'marker_list_id');
    }

    // Relación N:M con MarkerList
    public function lists()
    {
        return $this->belongsToMany(MarkerList::class, 'marker_list_markers', 'marker_id', 'marker_list_id');
    }

    // Reseñas del marcador
    public function reviews()
    {
        return $this->hasMany(MarkerReviews::class, 'marker_id');
    }

    // Promedio de estrellas
    public function averageStars()
    {
        return $this->reviews()->avg('review_stars');
    }
}
