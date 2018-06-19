<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerSelling extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'player_id', 'value', 'closes_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['closes_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'created_at', 'updated_at'
    ];

    /**
     * Attributes to be append to arrays.
     *
     * @var array
     */
    protected $appends = [
        'player_name'
    ];

    /**
     * Get player been sold
     *
     * @return Player
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get team that made the best offer
     *
     * @return Team
     */
    public function offeringTeam()
    {
        return $this->belongsTo(Team::class, 'best_offer_team');
    }

    /**
     * Get current offer value
     *
     * @return integer
     */
    public function getOfferValueAttribute()
    {
        if ($this->best_offer_value) {
            return $this->best_offer_value;
        } else {
            return $this->value;
        }
    }

    /**
     * Get player name
     *
     * @return String
     */
    public function getPlayerNameAttribute()
    {
        return $this->player->first_name . ' ' . $this->player->last_name;
    }
}
