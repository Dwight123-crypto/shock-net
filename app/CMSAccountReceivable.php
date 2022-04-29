<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMSAccountReceivable extends Model
{
    public $fillable = [
        'customer_id', 
        'project_id', 
        'billing_id', 
        'or_number',
        'amount', 
        'date'
    ];
}
