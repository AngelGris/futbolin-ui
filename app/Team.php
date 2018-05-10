<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Faker;
use Carbon\Carbon;
use DB;

class Team extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'user_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'formation' => 'array',
    ];

    /**
     * Carbon instance fields
     */
    protected $dates = ['last_trainning', 'trainer', 'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'strategy', 'last_trainning', 'trainer', 'trainning_count', 'created_at', 'updated_at'
    ];

    /**
     * Attribute limits for newly created players
     *
     * @var array
     */
    private $limits = [
        'ARQ' => [
            'goalkeeping' => [60, 100, 10],
            'defending' => [10, 70, 10],
            'dribbling' => [0, 30, 5],
            'heading' => [0, 30, 5],
            'jumping' => [0, 50, 10],
            'passing' => [20, 80, 10],
            'precision' => [0, 50, 10],
            'speed' => [30, 70, 5],
            'strength' => [10, 60, 10],
            'tackling' => [20, 50, 10]
        ],
        'DEF' => [
            'goalkeeping' => [0, 40, 10],
            'defending' => [60, 100, 10],
            'dribbling' => [20, 40, 5],
            'heading' => [60, 100, 10],
            'jumping' => [60, 100, 10],
            'passing' => [40, 80, 10],
            'precision' => [30, 80, 20],
            'speed' => [40, 80, 10],
            'strength' => [40, 80, 10],
            'tackling' => [60, 100, 10]
        ],
        'MED' => [
            'goalkeeping' => [0, 30, 10],
            'defending' => [40, 80, 10],
            'dribbling' => [60, 100, 10],
            'heading' => [40, 100, 10],
            'jumping' => [40, 100, 10],
            'passing' => [60, 100, 10],
            'precision' => [40, 100, 10],
            'speed' => [60, 100, 10],
            'strength' => [50, 90, 10],
            'tackling' => [40, 80, 10]
        ],
        'ATA' => [
            'goalkeeping' => [0, 20, 5],
            'defending' => [20, 60, 10],
            'dribbling' => [60, 90, 10],
            'heading' => [60, 100, 10],
            'jumping' => [60, 100, 10],
            'passing' => [40, 60, 10],
            'precision' => [60, 100, 10],
            'speed' => [40, 100, 20],
            'strength' => [60, 100, 10],
            'tackling' => [30, 70, 10]
        ]
    ];

    /**
     * Get the players associated with the team
     *
     * @return Collection Player
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get the team's positions
     *
     * @return Collection TournamentPosition
     */
    public function positions()
    {
        return $this->hasMany(TournamentPosition::class)->orderBy('category_id');
    }

    /**
     * Get the team's strategy
     *
     * @return Strategy
     */
    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    /**
     * Get the user associated with the team
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create new player in the team
     *
     * @param int $number
     * @param string $position
     * @param boolean $max_age
     * @return Player
     */
    public function createPlayer($number, $position, $max_age = FALSE)
    {
        $faker = Faker\Factory::create('es_AR');

        $position = strtoupper($position);

        if ($max_age) {
            $age = randomGauss(17, 20, 1);
        } else {
            $age = randomGauss(17, 32, 5);
        }
        $age_diff = 32 - $age;

        return $this->players()->create([
            'first_name' => $faker->firstName('male'),
            'last_name' => $faker->lastName('male'),
            'position' => $position,
            'age' => $age,
            'goalkeeping' => randomGauss(max(0, $this->limits[$position]['goalkeeping'][0] - $age_diff), max(0, $this->limits[$position]['goalkeeping'][1] - $age_diff), $this->limits[$position]['goalkeeping'][2]),
            'defending' => randomGauss(max(0, $this->limits[$position]['defending'][0] - $age_diff), max(0, $this->limits[$position]['defending'][1] - $age_diff), $this->limits[$position]['defending'][2]),
            'dribbling' => randomGauss(max(0, $this->limits[$position]['dribbling'][0] - $age_diff), max(0, $this->limits[$position]['dribbling'][1] - $age_diff), $this->limits[$position]['dribbling'][2]),
            'heading' => randomGauss(max(0, $this->limits[$position]['heading'][0] - $age_diff), max(0, $this->limits[$position]['heading'][1] - $age_diff), $this->limits[$position]['heading'][2]),
            'jumping' => randomGauss(max(0, $this->limits[$position]['jumping'][0] - $age_diff), max(0, $this->limits[$position]['jumping'][1] - $age_diff), $this->limits[$position]['jumping'][2]),
            'passing' => randomGauss(max(0, $this->limits[$position]['passing'][0] - $age_diff), max(0, $this->limits[$position]['passing'][1] - $age_diff), $this->limits[$position]['passing'][2]),
            'precision' => randomGauss(max(0, $this->limits[$position]['precision'][0] - $age_diff), max(0, $this->limits[$position]['precision'][1] - $age_diff), $this->limits[$position]['precision'][2]),
            'speed' => randomGauss(max(0, $this->limits[$position]['speed'][0] - $age_diff), max(0, $this->limits[$position]['speed'][1] - $age_diff), $this->limits[$position]['speed'][2]),
            'strength' => randomGauss(max(0, $this->limits[$position]['strength'][0] - $age_diff), max(0, $this->limits[$position]['strength'][1] - $age_diff), $this->limits[$position]['strength'][2]),
            'tackling' => randomGauss(max(0, $this->limits[$position]['tackling'][0] - $age_diff), max(0, $this->limits[$position]['tackling'][1] - $age_diff), $this->limits[$position]['tackling'][2]),
            'condition' => min(100, randomGauss(80, 100, 10)),
            'last_upgrade' => '',
            'number' => $number,
        ]);
    }

    /**
     * Injured players in the team
     *
     * @return Collection Player
     */
    public function getInjuredPlayersAttribute()
    {
        return $this->players()->where('recovery', '>', 0)->get();
    }

    /**
     * Get live match for team
     *
     * @return Collection Match
     */
    public function getLiveMatchAttribute()
    {
        $match = Matches::where(function ($query) {
                            $query->where('local_id', $this->id)
                                  ->orWhere('visit_id', $this->id);
                        })->where('type_id', '>', 2)->where('created_at', '>=', Carbon::now()->subMinutes(\Config::get('constants.LIVE_MATCH_DURATION')))->first();

        return $match;
    }

    /**
     * Replace player with a new one
     *
     * @param int $player_id
     * @return void
     */
    public function replacePlayer($player_id)
    {
        $player = $this->players->find($player_id);
        $arqs = $this->players->where('position', '=', 'ARQ')->count();
        $defs = $this->players->where('position', '=', 'DEF')->count();
        $meds = $this->players->where('position', '=', 'MED')->count();
        $atas = $this->players->where('position', '=', 'ATA')->count();
        $numbers = [];
        for ($i = 1; $i <= 99; $i++) {
            $numbers[$i] = 1;
        }
        foreach ($this->players as $p) {
            unset($numbers[$p['number']]);
        }
        $number = array_rand($numbers);
        unset($numbers);
        $position = FALSE;
        switch ($player->position) {
            case 'ARQ':
                if ($arqs <= 2) {
                    $position = 'ARQ';
                }
                break;
            case 'DEF':
                if ($defs <= 5) {
                    $position = 'DEF';
                }
                break;
            case 'MED':
                if ($meds <= 5) {
                    $position = 'MED';
                }
                break;
            case 'ATA':
                if ($atas <= 3) {
                    $position = 'ATA';
                }
                break;
        }

        if (!$position) {
            $probs = [];
            if ($arqs < 3) {
                $probs[] = 'ARQ';
            }
            if ($defs < 10) {
                $probs[] = 'DEF';
            }
            if ($meds < 10) {
                $probs[] = 'MED';
            }
            if ($atas < 6) {
                $probs[] = 'ATA';
            }
            $position = $probs[array_rand($probs)];
        }

        $newbie = $this->createPlayer($number, $position, TRUE);

        $pos = array_search($player->id, $this->formation);
        if (!empty($this->formation) && $pos >= 0) {
            $formation = $this->formation;
            $formation[$pos] = $newbie->id;
            $this->formation = $formation;
            $this->save();
        }

        Notification::create([
            'user_id' => $this->user->id,
            'title' => $player->first_name . ' ' . $player->last_name . ' se ha retirado',
            'message' => $player->first_name . ' ' . $player->last_name . ' ha decidido dejar las canchas y <a href="/jugador/' . $newbie->id . '/">' . $newbie->first_name . ' ' . $newbie->last_name . '</a> ha sido incorporado al equipo.',
        ]);

        $player->delete();
    }

    /**
     * Update if the team meats the requirements to play a match
     *
     * @return void
     */
    public function updatePlayable()
    {
        $playable = FALSE;

        if (count($this->formation) >= 11) {
            $lineup = array_slice($this->formation, 0, 11);
            $count = 0;
            foreach ($lineup as $player) {
                if ($player != 0) {
                    $count++;
                }
            }

            if ($count == 11) {
                $playable = TRUE;
            }
        }

        $this->playable = $playable;
        $this->save();
    }

    /**
     * Get team's average attribute
     *
     * @return int
     */
    public function getAverageAttribute()
    {
        if (count($this->formation) == 0) {
            return 0;
        } else {
            $lineup = array_slice($this->formation, 0, 11);
            $total = $count = 0;
            if ($this->user_id > 1) {
                foreach ($this->players as $player) {
                    if (in_array($player->id, $lineup))
                    {
                        $count++;
                        $total += $player->average;
                    }
                }
            } else {
                foreach ($lineup as $p) {
                    if ($p > 0) {
                        $count++;
                        $player = Player::find($p);
                        $total += $player->average;
                    }
                }
            }
            if ($count > 0) {
                return (int)($total / $count);
            } else {
                return 0;
            }
        }
    }

    /**
     * Get the public strategy info
     *
     * @return Array
     */
    public function getStrategyPublicAttribute()
    {
        $team = $this;
        $match = Matches::where(function($query) use ($team) {
            $query->where('local_id', '=', $team->id);
            $query->orWhere('visit_id', '=', $team->id);
        })
        ->where('type_id', '>', 1)
        ->latest()
        ->first();

        /**
         * Load strategy from last official match
         */
        $strategy = [];
        $data = getMatchLog($match['logfile']);
        if (!empty($data)) {
            if ($match->local_id == $this->id) {
                foreach ($data['local']['formation'] as $form) {
                    $player = Player::where('team_id', '=', $this->id)->where('number', '=', $form['number'])->first();
                    $strategy[] = [
                        'left' => number_format($form['top'] * 1.5, 2),
                        'top' => number_format($form['left'], 2),
                        'position' => (!empty($player) ? $player->position : ''),
                        'number' => (!empty($player) ? $form['number'] : ''),
                    ];
                }
            } else {
                foreach ($data['visit']['formation'] as $form) {
                    $player = Player::where('team_id', '=', $this->id)->where('number', '=', $form['number'])->first();
                    $strategy[] = [
                        'left' => (100 - $form['top']) * 1.5,
                        'top' => 100 - $form['left'],
                        'position' => (!empty($player) ? $player->position : ''),
                        'number' => (!empty($player) ? $form['number'] : ''),
                    ];
                }
            }
        }

        /**
         * If $strategy is empty, load it from the DB
         */
        if (empty($strategy)) {
            for ($i = 1; $i <= 11; $i++) {
                $player = Player::find($this->formation[$i - 1]);
                $strategy[] = [
                    'left' => ($this->strategy->{sprintf('j%02d_start_y', $i)} * 100 / 120 ) * 1.5,
                    'top' => $this->strategy->{sprintf('j%02d_start_x', $i)} * 100 / 90,
                    'position' => (!empty($player) ? $player['position'] : ''),
                    'number' => (!empty($player) ? $player['number'] : ''),
                ];
            }
        } else { // else, cut it to 11 players
            $strategy = array_slice($strategy, 0, 11);
        }

        return $strategy;
    }

    /**
     * Check if team can train
     *
     * @return boolean
     */
    public function getTrainableAttribute()
    {
        if ($this->last_trainning && $_SERVER['REQUEST_TIME'] - $this->last_trainning->timestamp < \Config::get('constants.TIME_TO_TRAIN')) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Get remaining time for the trainer
     *
     * @return void
     */
    public function getTrainerRemainingAttribute()
    {
        $now = Carbon::now();
        if ($this->trainer <= $now) {
            return 0;
        }

        $diff = $now->diffInDays($this->trainer);
        if ($diff > 0) {
            return $diff . ' días';
        }

        $diff = $now->diffInHours($this->trainer);
        if ($diff > 0) {
            return $diff . ' horas';
        }

        $diff = $now->diffInMinutes($this->trainer);
        if ($diff > 0) {
            return $diff . ' minutos';
        }

        $diff = $now->diffInSeconds($this->trainer);
        if ($diff > 0) {
            return $diff . ' segundos';
        }

    }

    /**
     * Check if team is in train spam
     *
     * @return boolean
     */
    public function getInTrainningSpamAttribute()
    {
        if (is_null($this->last_trainning) || $this->last_trainning->timestamp < $_SERVER['REQUEST_TIME'] - \Config::get('constants.TIME_TO_TRAIN') - \Config::get('constants.TRAIN_TIME_SPAM')) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Remaining time to become trainable
     *
     * @return int
     */
    public function getTrainableRemainingAttribute()
    {
        if ($this->trainable) {
            return 0;
        } else if ($this->last_trainning) {
            return \Config::get('constants.TIME_TO_TRAIN') - ($_SERVER['REQUEST_TIME'] - $this->last_trainning->timestamp);
        } else {
            return \Config::get('constants.TIME_TO_TRAIN');
        }
    }

    /**
     * Get the team's trophies
     *
     * @return Array int
     */
    public function getTrophiesAttribute()
    {
        $positions = $this->positions;
        $trophies = [];
        foreach ($positions as $position) {
            if (!$position->category->isOpen) {
                $trophies[] = $position;
            }
        }

        return $trophies;
    }

    /**
     * Get SVG file for the team shield
     *
     * @return URI
     */
    public function getShieldFileAttribute()
    {
        $file = '/img/shield/shield-' . sprintf('%02d', $this->shield) . '.svg';

        return $file;
    }

    /**
     * Train the team
     * @param boolean $force Force trainning (used for personal trainer)
     *
     * @return boolean Team trained
     */
    public function train($force = FALSE)
    {
        if ($force || $this->trainable) {
            if (!$this->inTrainningSpam) {
                $this->trainning_count = 1;
            } else {
                $this->trainning_count++;
            }
            $trainning_points = \Config::get('constants.TRAINNING_POINTS') * min(5, $this->trainning_count);

            DB::table('players')->where('team_id', $this->id)->where('recovery', 0)->increment('experience', $trainning_points);
            DB::table('players')->where('team_id', $this->id)->where('recovery', 0)->increment('stamina', $trainning_points);
            DB::table('players')->where('team_id', $this->id)->where('stamina', '>', 100)->update(['stamina' => 100]);
            $players = $this->players()->where('experience', '>=', 100)->get();
            foreach ($players as $player) {
                $player->upgrade();
            }

            $this->last_trainning = Carbon::now();
            $this->save();

            return TRUE;
        } else {
            return FALSE;
        }
    }
}
