<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scorer extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'team_id', 'player_id'
    ];

    /**
     * The relationships that should be included in arrays.
     *
     * @var array
     */
    protected $with = [
        'player'
    ];

    /**
     * @return mixed
     */
    public function player()
    {
        return $this->belongsTo(Player::class)->withTrashed();
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
