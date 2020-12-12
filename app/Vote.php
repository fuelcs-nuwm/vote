<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'question_id',
        'is_active',
        'vote_time',
        'started_at',
        'finished_at',
        'answer_yes',
        'answer_no',
        'answer_abstained',
        'didnt_vote',
    ];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}
