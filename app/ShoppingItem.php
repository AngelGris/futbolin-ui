<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'icon', 'in_shopping'
    ];

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Transactions with the shopping item
     *
     * @return Collection Transaction
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
