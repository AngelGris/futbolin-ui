<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerCard extends Model
{
    /**
     * Player that own the cards
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Associated suspension
     */
    public function suspension()
    {
        return $this->belongsto(Suspension::class);
    }
}
