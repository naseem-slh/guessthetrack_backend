<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;


    public function rooms()
    {
        return $this->belongsToMany(Room::class)->withPivot(['role', 'status', 'invited_at', 'invited_by'])->withTimestamps();
    }

    /**
     * Get rooms where user is owner
     */
    public function ownedRooms()
    {
        return $this->belongsToMany(Room::class)->wherePivot('role', 'owner');
    }

    /**
     * Get rooms where user is member (accepted invitations)
     */
    public function memberRooms()
    {
        return $this->belongsToMany(Room::class)->wherePivot('status', 'accepted');
    }

    /**
     * Get pending room invitations
     */
    public function pendingRoomInvitations()
    {
        return $this->belongsToMany(Room::class)->wherePivot('status', 'pending');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'spotify_access_token',
        'spotify_refresh_token',
        'spotify_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'spotify_access_token',
        'spotify_refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'spotify_token_expires_at' => 'datetime',
        ];
    }
    #TODO: user can only create one room
}
