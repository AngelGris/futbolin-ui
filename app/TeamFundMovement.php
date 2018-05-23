<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeamFundMovement extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_on'];

    /**
     * Carbon instance fields
     */
    protected $dates = ['created_at'];

    /**
     * Disabled timestamps
     *
     * @return boolean
     */
    public $timestamps = false;

    /**
     * Boot model
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}
