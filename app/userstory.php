<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class userstory extends Model
{
    protected $table="userstory";
    protected $fillable=['Name','Description'];
    public function project()
    {
       return $this->belongsTo('App\project');
    }
    public function tasks()
    {
        return $this->hasMany('App\task');
    }
}
