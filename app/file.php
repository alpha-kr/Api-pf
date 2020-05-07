<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class file extends Model
{
   protected $fillable=['ruta','id_comments','id_reunion'];
   protected $table='files';
}
