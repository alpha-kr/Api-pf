<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class file extends Model
{
   protected $fillable=['ruta','id_comments'];
   protected $table='files';
}
