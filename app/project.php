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
    public function meetings()
    {
        return $this->hasMany('App\meetings');
    }
    public function userstories()
    {
        return $this->hasmany('App\userstory','ProjectID');
    }
    public function tasks()
    {
       return $this->hasMany('App\task','ProjectID');
    }
    public function problems()
    {
        return $this->hasMany('App\problems','project');
    }
    public function sprints()
    {
        return $this->hasMany('App\sprint','Project_id');
    }
}
