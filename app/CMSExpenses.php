<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMSExpenses extends Model
{
    public $fillable = [
        'vendor_id', 
        'project_id', 
        'invoice_no', 
        'invoice_remarks', 
        'terms', 'period', 
        'description', 
        'amount', 
        'date', 
        'expenses_type'
    ];
}
