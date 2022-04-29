<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolesPermission extends Model
{
    public $fillable = ['roles_id','permission_key','permission_allowed'];
}
