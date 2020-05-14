<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class problems extends Model
{
  protected $table='problems';
  protected $fillable=['title','description','project'];
}
