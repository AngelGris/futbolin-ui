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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'tournament', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be appened to arrays.
     *
     * @var array
     */
    protected $appends = [
        'category_name', 'zone_name'
    ];

    /**
     * The relationships that should be included in arrays.
     *
     * @var array
     */
    protected $with = [
        'rounds', 'positions', 'scorers'
    ];

    /**
     * Positions for the category
     *
     * @return Collection TournamentPosition
     */
    public function positions()
    {
        return $this->hasMany(TournamentPosition::class, 'category_id')->orderBy('position');
    }

    /**
     * Category rounds
     *
     * @return Collection TournamentRound
     */
    public function rounds()
    {
        return $this->hasMany(TournamentRound::class, 'category_id')->orderBy('number');
    }

    /**
     * Scorers for category
     *
     * @return Collection Scorrer
     */
    public function scorers()
    {
        return $this->hasMany(Scorer::class, 'category_id')->where('player_id', '>', 36)->orderBy('goals', 'DESC');
    }

    /**
     * Category's tournament
     *
     * @return Tournament
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Category name (A, B, C, ...)
     *
     * @return char
     */
    public function getCategoryNameAttribute()
    {
        return chr($this->category + 64);
    }

    /**
     * Get if category is still open
     *
     * @return bool
     */
    public function getIsOpenAttribute()
    {
        $rounds = $this->rounds;

        return ($rounds[count($rounds) - 1]['datetime'] > $_SERVER['REQUEST_TIME']);
    }

    /**
     * Category name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return str_replace('Temporada', __('labels.season'),$this->tournament->name) . ' (' . __('labels.zone_and_category', ['zone' => $this->zone_name, 'category' => $this->category_name]) . ')';
    }

    /**
     * Category name with <br>
     *
     * @return string
     */
    public function getNameBrAttribute()
    {
        return $this->tournament->name . '<br> (' . __('labels.zone_and_category', ['zone' => $this->zone_name, 'category' => $this->category_name]) . ')';
    }

    /**
     * Zone name (I, II, III, ...)
     *
     * @return string
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
