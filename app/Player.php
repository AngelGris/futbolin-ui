<?php

namespace App;

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
        'last_upgrade' => 'array',
    ];

    protected $appends = ['name', 'short_name', 'average', 'cards_count', 'suspended'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_upgraded', 'deleted_at'];

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
     * Get player's name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $output = $this->first_name . ' ' . $this->last_name;
        if ($this->retiring) {
            $output .= ' <span class="fa fa-user-times" style="color:#f00;" data-toggle="tooltip" title="Se retira"></span>';
        }
        if ($this->cards) {
            if ($this->cards->cards >= \Config::get('constants.YELLOW_CARDS_SUSPENSION') - 1) {
                $output .= ' <span class="fa fa-square" style="color:#ff0;" data-toggle="tooltip" title="Tiene ' . (\Config::get('constants.YELLOW_CARDS_SUSPENSION') - 1) . ' amarillas"></span>';
            }
            if ($this->cards->suspension > 0) {
                $output .= ' <span class="fa fa-square" style="color:#f00;" data-toggle="tooltip" title="Suspendido"></span>';
            }
        }
        if ($this->recovery) {
            $output .= ' <span class="fa fa-medkit" style="color:#f00;" data-toggle="tooltip" title="Lesionado"> ' . $this->recovery . '</span> ';
        }
        if ($this->healed) {
            $output .= ' <span class="fa fa-plus-circle" style="color:#0a0;" data-toggle="tooltip" title="Tratado"></span>';
        }
        if ($this->upgraded) {
            $output .= ' <span class="fa fa-arrow-circle-up" style="color:#0a0;" data-toggle="tooltip" title="Subió de nivel"></span>';
        }
        if ($this->tired) {
            $output .= ' <span class="fa fa-arrow-down" style="color:#f00;" data-toggle="tooltip" title="Pocas energías"></span>';
        }
        return $output;
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
