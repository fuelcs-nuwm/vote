<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title','embedHtml', 'started','finished','vote_time'
    ];

    const EVENT_NOTSTARTED = 0;
    const EVENT_STARTED = 1;

    public function customers()
    {
        return $this->hasMany('App\Customer');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function groups()
    {
        return $this->hasMany('App\Group');
    }

}
