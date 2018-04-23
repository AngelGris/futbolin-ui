<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\CreditItem;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Notification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Attributes that should be mutated to dates
     */
    protected $dates = [
        'last_activity'
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this));
    }

    /**
     * Get the team associated with the user
     */
    public function team()
    {
        return $this->hasOne(Team::class);
    }

    /**
     * Get user's transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get user's messages
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'DESC');
    }

    /**
     * Add credits to account
     * @param integer $creditItemID
     *
     * @return integer Credits added
     */
    public function addCreditItem($creditItemId) {
        $creditItem = CreditItem::find($creditItemId);
        $this->credits += $creditItem->quantity;
        $this->save();

        return $creditItem->quantity;
    }

    /**
     * Create a new team for the user
     */
    public function createTeam($request)
    {
        if (!is_null($this->team)) {
            return $this->updateTeam($request);
        } else {
            return $this->team()->create($request->only(['name', 'short_name', 'stadium_name', 'primary_color', 'secondary_color', 'text_color', 'shield']));
        }
    }

    /**
     * Get user's admin privileges
     */
    public function getIsAdminAttribute()
    {
        return $this->is_administrator == 1;
    }

    /**
     * Get user's complete name
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Unread messages count
     */
    public function getUnreadMessagesAttribute()
    {
        return Notification::where('user_id', '=', $this->id)->whereNull('read_on')->count();
    }

    /**
     * Update user's team
     */
    public function updateTeam($request)
    {
        return $this->team()->update($request->only(['name', 'stadium_name', 'primary_color', 'secondary_color', 'text_color']));
    }
}
