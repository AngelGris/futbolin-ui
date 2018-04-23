<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'shopping_item_id', 'credits'
    ];

    /**
     * Shopping item bought
     */
    public function shoppingItem()
    {
        return $this->belongsTo(ShoppingItem::class);
    }

    /**
     * Transaction's user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
