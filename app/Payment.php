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

    /**
     * Credit item bought
     */
    public function creditItem()
    {
        return $this->belongsTo(CreditItem::class);
    }

    /**
     * Payment status
     */
    public function status()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }

    /**
     * Payment's user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
