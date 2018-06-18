<?php

namespace App;

use Carbon\Carbon;
use Faker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'team_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_upgrade'  => 'array',
        'retiring'      => 'boolean',
        'healed'        => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'cards', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Attributes to be append to arrays.
     *
     * @var array
     */
    protected $appends = [
        'name', 'short_name', 'average', 'cards_count', 'suspended', 'upgraded', 'transferable', 'bladeHandlerIcons'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_upgraded', 'deleted_at'
    ];

    /**
     * Attribute limits for newly created players
     *
     * @var array
     */
    const LIMITS = [
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
     * Boot model
     */
    protected static function boot() {
        parent::boot();

        static::deleting(function($player) {
             $player->selling()->delete();
        });
    }

    /**
     * Player's cards
     */
    public function cards()
    {
        return $this->hasOne(PlayerCard::class);
    }

    /**
     * Player's injury
     */
    public function injury()
    {
        return $this->belongsTo(Injury::class);
    }

    /**
     * Get the player's team
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get player's selling information
     */
    public function selling()
    {
        return $this->hasOne(PlayerSelling::class)
                    ->withDefault(function () {
                        return new \stdClass();
                    });
    }

    /**
     * Create new player
     *
     * @return Player
     */
    public static function create($team_id, $number, $position, $max_age = FALSE)
    {
        $faker = Faker\Factory::create('es_AR');

        $position = strtoupper($position);

        if ($max_age) {
            $age = randomGauss(17, 20, 1);
        } else {
            $age = randomGauss(17, 32, 5);
        }
        $age_diff = 32 - $age;

        $player = new Player();
        $player->team_id        = $team_id;
        $player->first_name     = $faker->firstName('male');
        $player->last_name      = $faker->lastName('male');
        $player->position       = $position;
        $player->age            = $age;
        $player->goalkeeping    = randomGauss(max(0, Player::LIMITS[$position]['goalkeeping'][0] - $age_diff), max(0, Player::LIMITS[$position]['goalkeeping'][1] - $age_diff), Player::LIMITS[$position]['goalkeeping'][2]);
        $player->defending      = randomGauss(max(0, Player::LIMITS[$position]['defending'][0] - $age_diff), max(0, Player::LIMITS[$position]['defending'][1] - $age_diff), Player::LIMITS[$position]['defending'][2]);
        $player->dribbling      = randomGauss(max(0, Player::LIMITS[$position]['dribbling'][0] - $age_diff), max(0, Player::LIMITS[$position]['dribbling'][1] - $age_diff), Player::LIMITS[$position]['dribbling'][2]);
        $player->heading        = randomGauss(max(0, Player::LIMITS[$position]['heading'][0] - $age_diff), max(0, Player::LIMITS[$position]['heading'][1] - $age_diff), Player::LIMITS[$position]['heading'][2]);
        $player->jumping        = randomGauss(max(0, Player::LIMITS[$position]['jumping'][0] - $age_diff), max(0, Player::LIMITS[$position]['jumping'][1] - $age_diff), Player::LIMITS[$position]['jumping'][2]);
        $player->passing        = randomGauss(max(0, Player::LIMITS[$position]['passing'][0] - $age_diff), max(0, Player::LIMITS[$position]['passing'][1] - $age_diff), Player::LIMITS[$position]['passing'][2]);
        $player->precision      = randomGauss(max(0, Player::LIMITS[$position]['precision'][0] - $age_diff), max(0, Player::LIMITS[$position]['precision'][1] - $age_diff), Player::LIMITS[$position]['precision'][2]);
        $player->speed          = randomGauss(max(0, Player::LIMITS[$position]['speed'][0] - $age_diff), max(0, Player::LIMITS[$position]['speed'][1] - $age_diff), Player::LIMITS[$position]['speed'][2]);
        $player->strength       = randomGauss(max(0, Player::LIMITS[$position]['strength'][0] - $age_diff), max(0, Player::LIMITS[$position]['strength'][1] - $age_diff), Player::LIMITS[$position]['strength'][2]);
        $player->tackling       = randomGauss(max(0, Player::LIMITS[$position]['tackling'][0] - $age_diff), max(0, Player::LIMITS[$position]['tackling'][1] - $age_diff), Player::LIMITS[$position]['tackling'][2]);
        $player->condition      = min(100, randomGauss(80, 100, 10));
        $player->last_upgrade   = '';
        $player->number         = $number;
        $player->value          = 100000;
        $player->save();

        return $player;
    }

    /**
     * Get player's average attribute
     *
     * @return float
     */
    public function getAverageAttribute()
    {
        switch ($this->position)
        {
            case 'ARQ':
                return (int)((($this->goalkeeping * 8) + $this->passing + $this->strength) / 10);
                break;
            case 'DEF':
                return (int)((($this->defending * 3) + ($this->jumping * 2) + $this->passing + $this->speed + ($this->tackling * 3)) / 10);
                break;
            case 'MED':
                return (int)((($this->dribbling * 3) + ($this->passing * 3) + $this->precision + $this->speed + $this->strength + $this->tackling) / 10);
                break;
            case 'ATA':
                return (int)(($this->dribbling + ($this->heading * 2) + ($this->jumping * 2) + ($this->precision * 2) + $this->speed + ($this->strength * 2)) / 10);
                break;
            default:
                return (int)(($this->goalkeeping + $this->defending + $this->dribbling + $this->heading + $this->jumping + $this->passing + $this->precision + $this->speed + $this->strength + $this->tackling) / 10);
                break;
        }
    }

    /**
     * Get player's handler for Blade
     *
     * @return string
     */
    public function getBladeHandlerIconsAttribute()
    {
        $output = '<div class="status">';
        if ($this->retiring) {
            $output .= '<span class="fa fa-user-times" style="color:#f00;"></span>';
        }
        if ($this->cards) {
            if ($this->cards->cards >= (\Config::get('constants.YELLOW_CARDS_SUSPENSION') - 1)) {
                $output .= '<span class="fa fa-square" style="color:#ff0;"></span>';
            }
            if ($this->cards->suspension) {
                $output .= '<span class="fa fa-square" style="color:#f00;"></span>';
            }
        }
        if ($this->recovery) {
            $output .= '<span class="fa fa-medkit" style="color:#f00;"></span>';
        }
        if ($this->stamina < 50) {
            $output .= '<span class="fa fa-arrow-down" style="color:#f00;"></span>';
        }
        $output .= '</div>';
        return '<div class="status">' . $this->iconsHtml(3, TRUE) . '</div>';
    }

    /**
     * Number of yellow cards the player has
     *
     * @return integer
     */
    public function getCardsCountAttribute()
    {
        if ($this->cards) {
            return $this->cards->cards;
        } else {
            return 0;
        }
    }

    /**
     * Get payer's free value
     *
     * @return integer
     */
    public function getFreeValueAttribute()
    {
        return max(100000, (int)($this->value / 2));
    }

    /**
     *
     */
    public function getLastUpgradeAttribute($value)
    {
        $value = json_decode($value);
        if (!empty($value)) {
            return $value;
        } else {
            return [];
        }
    }

    /**
     * Get player's name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ' ' . $this->iconsHtml();
    }

    /**
     * Get position complete name
     *
     * @return string
     */
    public function getPositionLongAttribute()
    {
        switch ($this->position)
        {
            case 'ARQ':
                return 'Arquero';
                break;
            case 'DEF':
                return 'Defensor';
                break;
            case 'MED':
                return 'Mediocampista';
                break;
            case 'ATA':
                return 'Atacante';
                break;
            default:
                return 'Sparring';
                break;
        }
    }

    /**
     * Get player's short name
     *
     * @return string
     */
    public function getShortNameAttribute()
    {
        $names = explode(' ', $this->first_name);

        $initials = '';
        foreach ($names as $name)
        {
            $initials .= mb_substr($name, 0, 1) . '. ';
        }

        return $initials . $this->last_name;
    }

    /**
     * Whether the player is suspended or not
     *
     * @return boolean
     */
    public function getSuspendedAttribute()
    {
        if ($this->cards) {
            return $this->cards->suspension > 0;
        } else {
            return FALSE;
        }
    }

    /**
     * Get player's suspension type
     *
     * @return string
     */
    public function getSuspensionTypeAttribute()
    {
        $suspension = Suspension::find($this->suspension_id);

        return $suspension->name;
    }

    /**
     * If stamina <= 50 then the player is tired
     *
     * @return boolean
     */
    public function getTiredAttribute()
    {
        return $this->stamina <= 50;
    }

    /**
     * Is player transferable
     *
     * @return boolean
     */
    public function getTransferableAttribute()
    {
        return !empty($this->selling->id);
    }

    /**
     * Is player available for injury treatment
     *
     * @return boolean
     */
    public function getTreatableAttribute()
    {
        if ($this->recovery > 0 && $this->healed == FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Number of matches a player recovers with treatment
     *
     * @return integer
     */
    public function getTreatmentImprovementAttribute()
    {
        return ceil($this->recovery / 2);
    }

    /**
     * Player was upgraded after last match
     *
     * @return boolean
     */
    public function getUpgradedAttribute()
    {
        $last_match = TournamentRound::where('datetime', '<', time())->orderBy('datetime', 'DESC')->first();
        if ($last_match && $this->last_upgraded > date('Y-m-d H:i:s', $last_match['datetime'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Get player's icons in HTML format
     *
     * @param integer $limit
     * @param boolean $short
     * @return string
     */
    public function iconsHtml($limit = 10, $short = FALSE)
    {
        $output = [];
        if ($this->retiring && count($output) < $limit) {
            $output[] = '<span class="fa fa-user-times" style="color:#f00;"' . ($short ? '' : ' data-toggle="tooltip" title="Se retira"') . '></span>';
        }
        if ($this->cards) {
            if ($this->cards->cards >= \Config::get('constants.YELLOW_CARDS_SUSPENSION') - 1 && count($output) < $limit) {
                $output[] = '<span class="fa fa-square" style="color:#ff0;"' . ($short ? '' : ' data-toggle="tooltip" title="Tiene ' . (\Config::get('constants.YELLOW_CARDS_SUSPENSION') - 1) . ' amarillas"') . '></span>';
            }
            if ($this->cards->suspension > 0 && count($output) < $limit) {
                $output[] = '<span class="fa fa-square" style="color:#f00;"' . ($short ? '' : ' data-toggle="tooltip" title="Suspendido"') . '></span>';
            }
        }
        if ($this->recovery && count($output) < $limit) {
            $output[] = '<span class="fa fa-medkit" style="color:#f00;"' . ($short ? '' : ' data-toggle="tooltip" title="Lesionado"') . '>' . ($short ? '' : ' ' . $this->recovery) . '</span>';
        }
        if ($this->healed && count($output) < $limit) {
            $output[] = '<span class="fa fa-plus-circle" style="color:#0a0;"' . ($short ? '' : ' data-toggle="tooltip" title="Tratado"') . '></span>';
        }
        if ($this->upgraded && count($output) < $limit) {
            $output[] = '<span class="fa fa-arrow-circle-up" style="color:#0a0;"' . ($short ? '' : ' data-toggle="tooltip" title="Subió de nivel"') . '></span>';
        }
        if ($this->tired && count($output) < $limit) {
            $output[] = '<span class="fa fa-arrow-down" style="color:#f00;"' . ($short ? '' : ' data-toggle="tooltip" title="Pocas energías"') . '></span>';
        }
        if ($this->transferable && count($output) < $limit) {
            $output[] = '<span class="fa fa-share-square-o" style="color:#0a0;"' . ($short ? '' : ' data-toggle="tooltip" title="Transferible"') . '></span>';
        }

        $glue = ($short ? '' : ' ');
        return implode($glue, $output);
    }

    /**
     * Set player free
     *
     * @return void
     */
    public function setFree()
    {
        $this->team_id = NULL;
        $this->value = $this->freeValue;
        $this->save();

        PlayerSelling::create([
            'player_id' => $this->id,
            'value'     => $this->value,
            'closes_at' => Carbon::now()->addDays(\Config::get('constants.PLAYERS_TRANSFERABLE_PERIOD'))
        ]);

        return $this;
    }

    /**
     * Start selling the player
     *
     * @return PlayerSelling
     */
    public function startSelling()
    {
        $selling = PlayerSelling::create([
            'player_id' => $this->id,
            'value'     => $this->value,
            'closes_at' => Carbon::now()->addDays(\Config::get('constants.PLAYERS_TRANSFERABLE_PERIOD'))
        ]);

        return $selling;
    }

    /**
     * Trasfer player to a new team
     *
     * @return boolean
     */
    public function transfer(Team $team, $value)
    {
        if ($this->team && $team->id == $this->team->id) {
            return FALSE;
        }

        // Notify selling team
        if ($this->team) {
            Notification::create([
                'user_id' => $this->team->user->id,
                'title' => $this->first_name . ' ' . $this->last_name . ' ha sido transferido',
                'message' => '<a href="/jugador/' . $this->id . '/">' . $this->first_name . ' ' . $this->last_name . '</a> ha sido transferido a <a href="/equipo/' . $team->id . '">' . $team->name . '</a> por ' . formatCurrency($value) . '.',
            ]);
        }

        // Notify buying team
        Notification::create([
            'user_id' => $team->user->id,
            'title' => 'Has comprado a ' . $this->first_name . ' ' . $this->last_name,
            'message' => 'Has comprado a <a href="/jugador/' . $this->id . '/">' . $this->first_name . ' ' . $this->last_name . '</a> por ' . formatCurrency($value) . ' y ya está a disposición del cuerpo técnico.',
        ]);

        $this->team_id = $team->id;
        $this->number = $team->freeNumber();
        $this->value = $value;
        $this->save();

        return TRUE;
    }

    /**
     * Treat injured player
     *
     * @return void
     */
    public function treat()
    {
        $this->recovery -= $this->treatment_improvement;
        if ($this->recovery > 0) {
            $this->healed = TRUE;
        }
        $this->save();
    }

    /**
     * Upgrade player
     *
     * @return void
     */
    public function upgrade()
    {
        if ($this->experience >= 100) {
            $points = 0;
            $age_limit = 7;
            $age_diff = $this->age - 17;
            if ($age_diff <= $age_limit) {
                $prob = 23 - (pow($age_diff, 2) / $age_limit);
            } else {
                $prob = pow(23 - $age_diff, 2) / (23 - $age_limit);
            }

            $points = (int)($prob / 10);
            $prob = ($prob % 10) * 100;
            if ($prob > 0) {
                if (mt_rand(0,99) <= $prob) {
                    $points++;
                }
            }

            $last_upgrade = [];
            $upgraded = '';
            for ($i = 0; $i < $points; $i++) {
                switch (mt_rand(0, 9)) {
                    case 0:
                        $this->goalkeeping = min($this->goalkeeping + 1, 100);
                        $upgraded = 'goalkeeping';
                        break;
                    case 1:
                        $this->defending = min($this->defending + 1, 100);
                        $upgraded = 'defending';
                        break;
                    case 2:
                        $this->dribbling = min($this->dribbling + 1, 100);
                        $upgraded = 'dribbling';
                        break;
                    case 3:
                        $this->heading = min($this->heading + 1, 100);
                        $upgraded = 'heading';
                        break;
                    case 4:
                        $this->jumping = min($this->jumping + 1, 100);
                        $upgraded = 'jumping';
                        break;
                    case 5:
                        $this->passing = min($this->passing + 1, 100);
                        $upgraded = 'passing';
                        break;
                    case 6:
                        $this->precision = min($this->precision + 1, 100);
                        $upgraded = 'precision';
                        break;
                    case 7:
                        $this->speed = min($this->speed + 1, 100);
                        $upgraded = 'speed';
                        break;
                    case 8:
                        $this->strength = min($this->strength + 1, 100);
                        $upgraded = 'strength';
                        break;
                    case 9:
                        $this->tackling = min($this->tackling + 1, 100);
                        $upgraded = 'tackling';
                        break;

                }

                if (empty($last_upgrade[$upgraded])) {
                    $last_upgrade[$upgraded] = 1;
                } else {
                    $last_upgrade[$upgraded]++;
                }
            }

            $this->experience -= 100;
            $this->last_upgrade = $last_upgrade;
            $this->last_upgraded = $_SERVER['REQUEST_TIME'];
            $this->save();
        }
    }
}
