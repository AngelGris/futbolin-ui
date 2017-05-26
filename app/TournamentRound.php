<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TournamentRound extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Round matches
     */
    public function matches()
    {
        return $this->hasMany(MatchesRound::class, 'round_id');
    }
}
