<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TournamentPosition extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'points', 'played', 'won', 'tied', 'lost', 'goals_favor', 'goals_against', 'goals_difference'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'category_id', 'category', 'team', 'created_at', 'updated_at'
    ];

    /**
     * The relationships that should be included in arrays.
     *
     * @var array
     */
    protected $appends = [
        'team_name', 'team_short_name'
    ];

    /**
     * Position category
     */
    public function category()
    {
        return $this->belongsTo(TournamentCategory::class, 'category_id');
    }

    /**
     * Position team
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get position and comparison with last_position
     *
     * @return string
     */
    public function getPositionFullAttribute()
    {
        $output = $this->position;
        if ($this->last_position > 0) {
            if ($this->last_position > $this->position) {
                $output .= ' <span class="fa fa-chevron-up position-full-up"></span>';
            } else if ($this->last_position < $this->position) {
                $output .= ' <span class="fa fa-chevron-down position-full-down"></span>';
            } else {
                $output .= ' <span class="fa fa-chevron-right position-full-right"></span>';
            }
        }
        return ($output);
    }

    /**
     * Get team name
     *
     * @return string
     */
    public function getTeamNameAttribute()
    {
        return $this->team->name;
    }

    /**
     * Get team short name
     *
     * @return string
     */
    public function getTeamShortNameAttribute()
    {
        return $this->team->short_name;
    }

    public function getTournamentNameAttribute()
    {
        return $this->category->name;
    }
}
