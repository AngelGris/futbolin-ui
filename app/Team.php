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
     * Get the user associated with the team
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team's strategy
     */
    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    /**
     * Get the players associated with the team
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Create new player in the team
     */
    public function createPlayer($number, $position)
    {
        $faker = Faker\Factory::create('es_AR');

        $position = strtoupper($position);

        $age = randomGauss(17, 32, 5);
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
            'number' => $number,
        ]);
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
     * Get SVG file for the team shield
     */
    public function getShieldFileAttribute()
    {
        $file = '/img/shield/shield-' . sprintf('%02d', $this->shield) . '.svg';

        return $file;
    }

    /**
     * Get if the team meats the requirements to play a match
     */
    public function getPlayableAttribute()
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

        return $playable;
    }
}
