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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'variables' => 'array'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user', 'user_id', 'read_on', 'variables', 'created_at', 'updated_at'
    ];

    /**
     * Attributes to be append to arrays.
     *
     * @var array
     */
    protected $appends = [
        'title', 'message', 'published', 'unread'
    ];

    public function getMessageAttribute()
    {
        return __('notifications.message_' . $this->notification_type, $this->variables);
    }

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
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    public function getTitleAttribute()
    {
        return __('notifications.title_' . $this->notification_type, $this->variables);
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

    /**
     * @param int $user_id
     * @param int $notification_type
     * @param array $variables
     * @return Notification|Model
     */
    public static function create(int $user_id, int $notification_type, array $variables) {
        $notification = new Notification();
        $notification->user_id = $user_id;
        $notification->notification_type = $notification_type;
        $notification->variables = $variables;
        $notification->save();

        return $notification;
    }
}
