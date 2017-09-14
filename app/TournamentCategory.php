<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TournamentCategory extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Positions for the category
     */
    public function positions()
    {
        return $this->hasMany(TournamentPosition::class, 'category_id')->orderBy('position');
    }

    /**
     * Category rounds
     */
    public function rounds()
    {
        return $this->hasMany(TournamentRound::class, 'category_id')->orderBy('number');
    }

    /**
     * Scorers for category
     */
    public function scorers()
    {
        return $this->hasMany(Scorer::class, 'category_id')->orderBy('goals', 'DESC');
    }

    /**
     * Category's tournament
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Category name (A, B, C, ...)
     */
    public function getCategoryNameAttribute()
    {
        return chr($this->category + 64);
    }

    /**
     * Get if category is still open
     */
    public function getIsOpenAttribute()
    {
        $rounds = $this->rounds;


        return ($rounds[37]['datetime'] > $_SERVER['REQUEST_TIME']);
    }

    /**
     * Category name
     */
    public function getNameAttribute()
    {
        return $this->tournament->name . ' (Zona ' . $this->zone_name . ' Cat. ' . $this->category_name . ')';
    }

    /**
     * Zone name (I, II, III, ...)
     */
    public function getZoneNameAttribute()
    {
        $table = [
            'M'=>1000,
            'CM'=>900,
            'D'=>500,
            'CD'=>400,
            'C'=>100,
            'XC'=>90,
            'L'=>50,
            'XL'=>40,
            'X'=>10,
            'IX'=>9,
            'V'=>5,
            'IV'=>4,
            'I'=>1
        ];
        $return = '';
        $integer = $this->zone;
        while ($integer > 0)
        {
            foreach ($table as $rom => $arb)
            {
                if ($integer >= $arb)
                {
                    $integer -= $arb;
                    $return .= $rom;
                    break;
                }
            }
        }

        return $return;
    }
}
