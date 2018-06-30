<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketTransaction extends Model
{
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'player_id', 'seller_id', 'buyer_id', 'value', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'player_id', 'seller_id', 'buyer_id'
    ];

    /**
     * The relationships that should be included in arrays.
     *
     * @var array
     */
    protected $with = [
        'player', 'seller', 'buyer'
    ];

    /**
     * Buying team
     */
    public function buyer()
    {
        return $this->belongsTo(Team::class, 'buyer_id');
    }

    /**
     * Player in transaction
     */
    public function player()
    {
        return $this->belongsTo(Player::class)->withTrashed();
    }

    /**
     * Selling team
     */
    public function seller()
    {
        return $this->belongsto(Team::class, 'seller_id');
    }
}
