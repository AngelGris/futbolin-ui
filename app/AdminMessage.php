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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'valid_from', 'valid_to', 'updated_at'
    ];

    /**
     * Attributes to be append to arrays.
     *
     * @var array
     */
    protected $appends = [
        'published'
    ];

    /**
     * Get published as human readable
     */
    public function getPublishedAttribute()
    {
        return $this->valid_from->diffForHumans();
    }
}
