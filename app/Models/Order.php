<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'number', 'status', 'payment_status'
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $order->number = Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(ProductOrder::class);
    }


    public function getTotalPriceAttribute()
    {
        return $this->products->sum(fn($item) => $item->price * $item->quantity);
    }
}