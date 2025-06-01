<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendGroup extends Model
{
    use HasFactory;

    protected $table = 'friend_groups';

    protected $fillable = [
        'name',
        'owner_user_id',
    ];

    // Relación N:M con usuarios (miembros del grupo)
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friend_groups_friends', 'friend_group_id', 'user_id');
    }

    // Relación 1:N con el usuario que creó el grupo
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
