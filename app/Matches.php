<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    /**
     * Get local team
     */
    public function local()
    {
        return $this->hasOne(Team::class, 'foreign_key', 'local_id');
    }

    /**
     * Get visit team
     */
    public function visit()
    {
        return $this->hasOne(Team::class, 'foreign_key', 'visit_id');
    }
}
