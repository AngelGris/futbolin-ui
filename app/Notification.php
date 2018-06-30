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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user', 'user_id', 'read_on', 'created_at', 'updated_at'
    ];

    /**
     * Attributes to be append to arrays.
     *
     * @var array
     */
    protected $appends = [
        'published', 'unread'
    ];

    /**
     * Get published as human readable
     *
     * @return String
     */
    public function getPublishedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get if the notification is unread
     *
     * @return boolean
     */
    public function getUnreadAttribute()
    {
        return is_null($this->read_on);
    }
}
