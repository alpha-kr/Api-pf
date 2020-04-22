<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tokenUser extends Model
{
   protected $table='token-user';
   protected $fillable=['user','token'];
   public function users()
   {

       return $this->belongsTo('App\user','user');
   }
}
