<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Faker;

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
    protected $dates = ['last_trainning', 'created_at', 'updated_at'];

    /**
     * Attribute limits for newly created players
     *
     * @var arrau
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
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get the team's positions
     */
    public function positions()
    {
        return $this->hasMany(TournamentPosition::class)->orderBy('category_id');
    }

    /**
     * Get the team's strategy
     */
    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    /**
     * Get the user associated with the team
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create new player in the team
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
     * Replace player with a new one
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

        if (!empty($this->formation) && $pos = array_search($player->id, $this->formation)) {
            $formation = $this->formation;
            $formation[$pos] = 0;
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
     * Check if team can train
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
     * Check if team is in train spam
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
     */
    public function getShieldFileAttribute()
    {
        $file = '/img/shield/shield-' . sprintf('%02d', $this->shield) . '.svg';

        return $file;
    }
}
