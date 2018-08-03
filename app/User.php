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
     * Attributes that should be mutated to dates
     *
     * @var array
     */
    protected $dates = [
        'last_activity'
    ];

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
        'password', 'remember_token', 'email', 'credits', 'last_activity', 'notifications', 'is_administrator', 'created_at', 'updated_at'
    ];

    /**
     * The relationships that should be included in arrays.
     *
     * @var array
     */
    protected $with = [
        'team'
    ];

    /**
     * Get all API tokens
     *
     * @return Collection ApiToken
     */
    public function apiTokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    /**
     *
     */
    public function following()
    {
        return $this->belongsToMany(PlayerSelling::class);
    }

    /**
     * Get the team associated with the user
     *
     * @return Team
     */
    public function team()
    {
        return $this->hasOne(Team::class);
    }

    /**
     * Get user's transactions
     *
     * @return Collection Transaction
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get user's messages
     *
     * @return Collection Notification
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
     *
     * @return Team
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
     * Start following a player
     *
     * @param integer $playerId
     * @return boolean
     */
    public function followPlayer($playerSellingId)
    {
        $this->following()->syncWithoutDetaching([$playerSellingId]);
    }

    /**
     * Generate API token for user in a given device
     *
     * @param string $deviceId
     * @param string $deviceName
     * @return string
     */
    public function generateToken($deviceId, $deviceName)
    {
        ApiToken::where('device_id', $deviceId)->delete();

        $apiToken = new ApiToken;
        $apiToken->user_id = $this->id;
        $apiToken->device_id = $deviceId;
        $apiToken->device_name = $deviceName;
        $apiToken->used_on = date('Y-m-d H:i:s');
        $apiToken->api_token = str_random(60);
        $apiToken->save();

        return $apiToken->api_token;
    }

    /**
     * Get a list of the followed players
     */
    public function getFollowingListAttribute()
    {
        $follows = $this->following()->select('player_id')->get();
        $following = [];
        foreach ($follows as $follow) {
            $following[] = $follow->player_id;
        }

        return $following;
    }

    /**
     * Get user's admin privileges
     *
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->is_administrator == 1;
    }

    /**
     * Get user's complete name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Unread messages count
     *
     * @return Notification
     */
    public function getUnreadMessagesAttribute()
    {
        return Notification::where('user_id', '=', $this->id)->whereNull('read_on')->count();
    }

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
     * Stop following a player
     *
     * @param integer $playerId
     * @return boolean
     */
    public function unfollowPlayer($playerSellingId)
    {
        $this->following()->detach($playerSellingId);
    }

    /**
     * Update user's team
     *
     * @return Team
     */
    public function updateTeam($request)
    {
        return $this->team()->update($request->only(['name', 'short_name', 'stadium_name', 'primary_color', 'secondary_color', 'text_color', 'shield']));
    }
}
