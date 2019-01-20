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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'variables' => 'array'
    ];

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'team_id'
    ];

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

    public function getDescriptionAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return __('messages.fund_movements_' . $this->movement_type, $this->variables);
    }
}
