<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoteAnswer extends Model
{
    protected $fillable = [
        'vote_id',
        'user_id',
        'answer',
    ];
}
