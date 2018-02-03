<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Tournament's categories
     */
    public function tournamentCategories()
    {
        return $this->hasMany(TournamentCategory::class);
    }

    public function createCategory($category, $zone, $teams)
    {
        /**
         * Create category
         */
        $category = $this->tournamentCategories()->create(['category' => $category, 'zone' => $zone]);

        /**
         * Convert teams OBJ to INT
         */
        foreach ($teams as $key => $team) {
            if (is_object($team)) {
                $teams[$key] = $team->id;
            }
        }

        /**
         * Add teams
         */
        $i = 0;
        foreach($teams as $team) {
            TournamentPosition::create(['category_id' => $category->id, 'team_id' => $team, 'position' => ++$i]);
        }

        /**
         * Create fixture
         */
        // Tournament starting round
        $round_time = strtotime('next monday') + 72000;
        // Back phase starting round
        $rounds_in_phase = \Config::get('constants.TEAMS_PER_CATEGORY') - 1;
        $phase_days = ((int)(($rounds_in_phase) / 3) * 7) + (($rounds_in_phase % 3) * 2);
        $round_time_back = strtotime(sprintf('next monday +%d days', $phase_days)) + 72000;
        for ($i = 0; $i < $rounds_in_phase; $i++) {
            $round_number = $i + 1;
            $round1 = \DB::table('tournament_rounds')->insertGetId([
                'category_id' => $category->id,
                'number' => $round_number,
                'datetime' =>  $round_time,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $round2 = \DB::table('tournament_rounds')->insertGetId([
                'category_id' => $category->id,
                'number' => $round_number + $rounds_in_phase,
                'datetime' =>  $round_time_back,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $aux = [$teams[0]];
            for ($j = 1; $j < \Config::get('constants.TEAMS_PER_CATEGORY'); $j++) {
                if (($j + $i) < \Config::get('constants.TEAMS_PER_CATEGORY')) {
                    $aux[] = $teams[$j + $i];
                } else {
                    $aux[] = $teams[($j + $i + 1) % \Config::get('constants.TEAMS_PER_CATEGORY')];
                }
            }

            for ($j = 0; $j < \Config::get('constants.TEAMS_PER_CATEGORY') / 2; $j++) {
                if ($i % 2) {
                    $team1 = $aux[$j];
                    $team2 = $aux[\Config::get('constants.TEAMS_PER_CATEGORY') - $j - 1];
                } else {
                    $team1 = $aux[\Config::get('constants.TEAMS_PER_CATEGORY') - $j - 1];
                    $team2 = $aux[$j];
                }

                \DB::table('matches_rounds')->insert([
                    [
                        'round_id' => $round1,
                        'local_id' => $team1,
                        'visit_id' => $team2,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'round_id' => $round2,
                        'local_id' => $team2,
                        'visit_id' => $team1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                ]);
            }

            if ($round_number % 3) {
                $round_time += 172800;
            } else {
                $round_time += 259200;
            }

            if (($rounds_in_phase + $round_number) % 3) {
                $round_time_back += 172800;
            } else {
                $round_time_back += 259200;
            }
        }
    }
}