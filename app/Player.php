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

    protected $appends = ['short_name', 'average'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the player's team
     */
    public function owner()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get player's average attribute
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
     * Get position complete name
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
     * Get player's name
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get player's short name
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
}
