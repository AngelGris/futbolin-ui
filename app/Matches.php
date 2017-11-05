<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    /**
     * Get local team
     */
    public function local()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get visit team
     */
    public function visit()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Match's category
     */
    public function getCategoryAttribute()
    {
        $category = TournamentCategory::join('tournament_rounds', 'tournament_rounds.category_id', '=', 'tournament_categories.id')
                                      ->join('matches_rounds', 'matches_rounds.round_id', '=', 'tournament_rounds.id')
                                      ->where('matches_rounds.match_id', $this->id)
                                      ->first();

        return $category;
    }

    /**
     * Match's round
     */
    public function getRoundAttribute()
    {
        $round = TournamentRound::join('matches_rounds', 'matches_rounds.round_id', '=', 'tournament_rounds.id')
                                ->where('matches_rounds.match_id', $this->id)
                                ->first();

        return $round;
    }

    /**
     * Load last friendly match between 2 teams (fixed local/visit)
     */
    public static function loadLastFriendlyMatch($local_id, $visit_id) {
        $match = \DB::table('matches')
            ->select('id', 'logfile', 'created_at')
            ->where('type_id', '<=', 2)
            ->where('local_id', '=', $local_id)
            ->where('visit_id', '=', $visit_id)
            ->latest()
            ->first();

        return $match;
    }

    /**
     * Load last match for team
     */
    public static function loadLastMatches($team_id, $limit = 10) {
        $matches = \DB::table('matches')
            ->select('*')
            ->where('local_id', '=', $team_id)
            ->orWhere('visit_id', '=', $team_id)
            ->latest()
            ->limit($limit)
            ->get();

        return $matches;
    }
}