<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class meetings extends Model
{
    protected $fillable=['Name','descripcion','StartDate','EndDate','Project_id'];
    protected $table='meetings';
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    public function project()
    {
        return $this->belongsTo('App\project','Project_id');
    }
}
