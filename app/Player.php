<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'team_id'];

    /**
     * Get the player's team
     */
    public function owner()
    {
        return $this->belongsTo(Team::class);
    }
}
