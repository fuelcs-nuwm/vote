<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'title','event_id',
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class, 'question_id', 'id');
    }
}
