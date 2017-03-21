<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    /**
     * Get the teams using this strategy
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
