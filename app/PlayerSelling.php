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
}
