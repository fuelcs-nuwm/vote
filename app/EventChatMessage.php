<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'message_id',
        'message',
        'date',
    ];

    public function replies()
    {
        return $this->hasMany(EventChatMessage::class, 'message_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
