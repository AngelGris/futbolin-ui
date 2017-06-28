<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get published as human readable
     */
    public function getPublishedAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
