<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class task extends Model
{
   protected $table="tasks";
   protected $fillable=['Name','Status','Description','ProjectID','Sprint_id','UserStoryID'];

   public function users()
   {
      return $this->belongsToMany('App\User','task_user','TaskID','UserID');
   }
   public function sprint()
   {
      return $this->belongsTo('App\sprint','Sprint_id');
   }
   public function project()
   {
      return $this->belongsTo('App\project');

   }
   public function userstory()
   {
      return $this->belongsTo('App\userstory','UserStoryID');
   }
   public function comments()
   {
      return $this->hasMany('App\commets','id_task');
   }
   public function commentes_files()
   {
      return $this->comments()->with('files');
   }
}
