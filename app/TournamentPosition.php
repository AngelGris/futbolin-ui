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
}
