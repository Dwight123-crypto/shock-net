<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMSAdditionalWork extends Model
{
    public $fillable = [
        'project_id', 
        'customer_id', 
        'date', 
        'amount',
        'description'
    ];
}
