<?php

// app/Models/EventUserRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventUserRequest extends Model
{
    protected $fillable = ['event_id', 'user_id', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
