<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scorer extends Model
{
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
