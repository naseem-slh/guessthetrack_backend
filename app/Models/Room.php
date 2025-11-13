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
        return $this->belongsToMany(User::class);
    }
    /**
     * The creator/owner of the room (one-to-many inverse)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
