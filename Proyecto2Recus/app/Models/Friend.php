<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $table = 'friends';

    protected $fillable = [
        'request_status',
        'sender_user_id',
        'reciver_user_id',
        'friend_group_id',
    ];

    protected $casts = [
        'request_status' => 'boolean',
    ];

    // Usuario que envía la solicitud de amistad
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    // Usuario que recibe la solicitud de amistad
    public function reciver()
    {
        return $this->belongsTo(User::class, 'reciver_user_id');
    }

    // Grupo de amigos al que pertenece esta relación (opcional)
    public function friendGroup()
    {
        return $this->belongsTo(FriendGroup::class, 'friend_group_id');
    }
}
