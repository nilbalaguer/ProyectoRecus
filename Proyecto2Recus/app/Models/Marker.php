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

    // Usuario que creó el marcador
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación 1:N con MarkerList (en caso de que uses esta columna directa además de la N:M)
    public function markerList()
    {
        return $this->belongsTo(MarkerList::class, 'marker_list_id');
    }

    // Relación N:M con MarkerList (tabla pivot marker_list_markers)
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
