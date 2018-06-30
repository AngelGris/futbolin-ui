<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    /**
     * Get user for API token
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
