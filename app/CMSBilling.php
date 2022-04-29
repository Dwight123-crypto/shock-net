<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMSBilling extends Model
{
    public $fillable = [
        'customer_id', 
        'project_id', 
        'billing_invoice_no', 
        'amount', 
        'date', 
        'status'
    ];
}
