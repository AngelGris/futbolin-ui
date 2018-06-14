<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MatchesRound extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'match_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'round_id', 'local_id', 'visit_id', 'match_id', 'created_at', 'updated_at'
    ];

    /**
     * The relationships that should be included in arrays.
     *
     * @var array
     */
    protected $with = [
        'match'
    ];

    /**
     * Local
     */
    public function local()
    {
        return $this->belongsTo(Team::class, 'local_id');
    }

    /**
     * Visit
     */
    public function visit()
    {
        return $this->belongsTo(Team::class, 'visit_id');
    }

    /**
     * Match log
     */
    public function match() {
        return $this->belongsTo(Matches::class);
    }

    /**
     * Local team goals
     */
    public function getLocalGoalsAttribute()
    {
        if ($this->match_id) {
            return $this->match->local_goals;
        } else {
            return '-';
        }
    }

    /**
     * Match logfile
     */
    public function getLogfileAttribute()
    {
        return $this->match->logfile;
    }

    /**
     * Visit team goals
     */
    public function getVisitGoalsAttribute()
    {
        if ($this->match_id) {
            return $this->match->visit_goals;
        } else {
            return '-';
        }
    }
}
