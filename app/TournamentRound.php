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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'created_at', 'updated_at'
    ];

    /**
     * The relationships that should be included in arrays.
     *
     * @var array
     */
    protected $with = [
        'matches'
    ];

    /**
     * Round matches
     */
    public function matches()
    {
        return $this->hasMany(MatchesRound::class, 'round_id');
    }
}
