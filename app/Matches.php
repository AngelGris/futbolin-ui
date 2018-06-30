<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'created_at', 'updated_at'
    ];

    /**
     * Get local team
     *
     * @return Team
     */
    public function local()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get visit team
     *
     * @return Team
     */
    public function visit()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get match assistance
     *
     * @return integer
     */
    public function getAssistanceAttribute()
    {
        $assistance = MatchesRound::select('assistance')->where('match_id', $this->id)->first();
        if (is_null($assistance)) {
            return 0;
        } else {
            return $assistance->assistance;
        }
    }

    /**
     * Match's category
     *
     * @return TournamentCategory
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
     * Get match incomes
     *
     * @return integer
     */
    public function getIncomesAttribute()
    {
        $incomes = MatchesRound::select('incomes')->where('match_id', $this->id)->first();
        if (is_null($incomes)) {
            return 0;
        } else {
            return $incomes->incomes;
        }
    }

    /**
     * Match's round
     *
     * @return TournamentRound
     */
    public function getRoundAttribute()
    {
        $round = TournamentRound::join('matches_rounds', 'matches_rounds.round_id', '=', 'tournament_rounds.id')
                                ->where('matches_rounds.match_id', $this->id)
                                ->first();

        return $round;
    }

    /**
     * Load last friendly match between 2 teams (fixed local/visit)*
     *
     * @param integer $local_id
     * @param integer $visit_id
     * @return Matches
     */
    public static function loadLastFriendlyMatch($local_id, $visit_id)
    {
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
     *
     * @param integer $team_id
     * @param integer $limit
     * @return Collection Matches
     */
    public static function loadLastMatches($team_id, $limit = 10)
    {
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