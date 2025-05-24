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

    public function friends()
    {
        return $this->belongsToMany(User::class, 'friend_groups_friends', 'friend_group_id', 'id_friend');
    }
}
