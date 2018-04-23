<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Transactions with the shopping item
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
