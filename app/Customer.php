<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'email','event_id',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'customer_group');
    }

}
