<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sprint extends Model
{
    protected  $table="sprints";
    public function tasks()
    {
        return $this->hasMany('App\task');
    }
}
