<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManageRoles extends Model
{
    public $fillable = ['user_id','role_name'];
}
