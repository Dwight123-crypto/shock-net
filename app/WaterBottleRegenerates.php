<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaterBottleRegenerates extends Model
{
    public $fillable = ['entry_no', 'order_qty', 'refill_bottle', 'others_qty','container_qty','dealer_qty'];
}
