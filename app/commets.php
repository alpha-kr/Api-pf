<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class commets extends Model
{
    protected $table='comments';
    protected $fillable=['Description','id_user','id_task','id_userstory'];
    public function files()
    {
       return $this->hasMany('App\file','id_comments');
    }
 
}
