<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'user_id'];

    /**
     * Get the user associated with the team
     */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
