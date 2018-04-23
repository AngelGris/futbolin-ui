<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'credit_item_id', 'method', 'amount_total', 'amount_earnings', 'payment_status_id', 'description'
    ];
}
