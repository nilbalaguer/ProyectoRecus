<?php

namespace App\Models;

use App\Notifications\UserResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use App\Models\UserAssignment;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'last_lng',
        'last_lat',
        'desc',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_lng' => 'double',
        'last_lat' => 'double',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }

    public function assignments()
    {
        return $this->hasMany(UserAssignment::class, 'user_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images/users')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if (env('RESIZE_IMAGE') === true) {
            $this->addMediaConversion('resized-image')
                ->width(env('IMAGE_WIDTH', 312))
                ->height(env('IMAGE_HEIGHT', 312));
        }
    }

    // Relaciones de amigos
    public function sentFriendRequests()
    {
        return $this->hasMany(Friend::class, 'sender_user_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friend::class, 'reciver_user_id');
    }

    public function friendsReceived()
    {
        return $this->hasMany(Friend::class, 'reciver_user_id')->with('sender');
    }

    public function friendsSent()
    {
        return $this->hasMany(Friend::class, 'sender_user_id')->with('reciver');
    }

    // Grupos de amigos a los que pertenece (N:M)
    public function friendGroups()
    {
        return $this->belongsToMany(FriendGroup::class, 'friend_groups_friends', 'friends_id', 'friend_group_id');
    }

    // Grupos de amigos que ha creado (1:N)
    public function ownedFriendGroups()
    {
        return $this->hasMany(FriendGroup::class, 'owner_user_id');
    }

    // Listas de marcadores que ha creado (1:N)
    public function markerLists()
    {
        return $this->hasMany(MarkerList::class, 'owner_user_id');
    }

    // Marcadores creados por el usuario (1:N)
    public function markers()
    {
        return $this->hasMany(Marker::class, 'user_id');
    }

    // Reseñas realizadas por el usuario (1:N)
    public function markerReviews()
    {
        return $this->hasMany(MarkerReviews::class, 'user_id');
    }
}
