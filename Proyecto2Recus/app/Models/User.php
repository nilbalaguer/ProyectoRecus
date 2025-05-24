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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
        if (env(key: 'RESIZE_IMAGE') === true) {
            $this->addMediaConversion('resized-image')
                ->width(env('IMAGE_WIDTH', 312))
                ->height(env('IMAGE_HEIGHT', 312));
        }
    }

    public function sentFriendRequests() {
        return $this->hasMany(Friend::class, 'sender_user_id');
    }
    
    public function receivedFriendRequests() {
        return $this->hasMany(Friend::class, 'reciver_user_id');
    }

    public function friendsReceived()
    {
        return $this->hasMany(Friend::class, 'reciver_user_id')
                    ->with('sender');
    }

    public function friendsSent()
    {
        return $this->hasMany(Friend::class, 'sender_user_id')
                    ->with('reciver');
    }

    public function friendGroups()
    {
        return $this->belongsToMany(FriendGroup::class, 'friend_groups_friends', 'id_friend', 'friend_group_id');
    }

}
