<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the team associated with the user
     */
    public function team()
    {
        return $this->hasOne(Team::class);
    }

    /**
     * Create a new team for the user
     */
    public function createTeam($request)
    {
        if (!is_null($this->team)) {
            return $this->updateTeam($request);
        } else {
            return $this->team()->create($request->only(['name', 'stadium_name', 'primary_color', 'secondary_color']));
        }
    }

    /**
     * Update user's team
     */
    public function updateTeam($request)
    {
        return $this->team()->update($request->only(['name', 'stadium_name', 'primary_color', 'secondary_color']));
    }
}
