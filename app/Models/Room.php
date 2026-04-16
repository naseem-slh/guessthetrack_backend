<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name','creator_id'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['role', 'status', 'invited_at', 'invited_by'])->withTimestamps();
    }

    /**
     * Get only accepted members
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->wherePivot('status', 'accepted')->withPivot(['role', 'status', 'invited_at', 'invited_by']);
    }

    /**
     * Get pending invitations
     */
    public function pendingInvitations()
    {
        return $this->belongsToMany(User::class)->wherePivot('status', 'pending')->withPivot(['role', 'status', 'invited_at', 'invited_by']);
    }

    /**
     * Get the owner of the room
     */
    public function owner()
    {
        return $this->belongsToMany(User::class)->wherePivot('role', 'owner');
    }
    /**
     * The creator/owner of the room (one-to-many inverse)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function gameSetting()
    {
        return $this->hasOne(GameSetting::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
