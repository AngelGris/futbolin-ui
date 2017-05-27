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
         * Add teams
         */
        foreach($teams as $team) {
            TournamentPosition::create(['category_id' => $category->id, 'team_id' => $team, 'position' => 1]);
        }

        /**
         * Create fixture
         */
        $round_time = strtotime('next monday') + 72000;
        for ($i = 0; $i < 19; $i++) {
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
                'number' => $round_number + 19,
                'datetime' =>  $round_time,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $aux = [$teams[0]];
            for ($j = 1; $j < 20; $j++) {
                if (($j + $i) < 20) {
                    $aux[] = $teams[$j + $i];
                } else {
                    $aux[] = $teams[($j + $i + 1) % 20];
                }
            }

            for ($j = 0; $j < 10; $j++) {
                if ($i % 2) {
                    $team1 = $aux[$j];
                    $team2 = $aux[19 - $j];
                } else {
                    $team1 = $aux[19 - $j];
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
        }
    }
}