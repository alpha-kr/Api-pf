<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class project extends Model
{
    protected  $table="projects";
    protected $fillable=['Name','Des','StartDate','EndDate'];
    public function user()
    {
        return $this->belongsToMany('App\User')->withPivot('Role');
    }
    public function userstories()
    {
        return $this->hasmany('App\userstory','ProjectID');
    }
    public function tasks()
    {
       return $this->hasMany('App\task','ProjectID');
    }
}
