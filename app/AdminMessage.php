<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminMessage extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Carbon instance fields
     */
    protected $dates = ['valid_from', 'created_at', 'updated_at'];

    /**
     * Get published as human readable
     */
    public function getPublishedAttribute()
    {
        return $this->valid_from->diffForHumans();
    }
}
