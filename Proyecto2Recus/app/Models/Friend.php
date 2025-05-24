<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Friend extends Pivot
{
    use HasFactory;

    protected $table = 'friends';

    protected $fillable = [
        'request_status',
        'sender_user_id',
        'reciver_user_id',
        'friend_group_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function reciver() {
        return $this->belongsTo(User::class, 'reciver_user_id');
    }

}
