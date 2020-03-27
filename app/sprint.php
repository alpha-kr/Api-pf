<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sprint extends Model
{
    protected $fillable=['Name','Project_id','StartDate','EndDate'];
    protected  $table="sprints";
    
    public function task()
    {
        return $this->hasMany('App\task','Sprint_id');
    }
    public function tasks()
    {
       return $this->task()->with('commentes_files');
    }
 
    
}
