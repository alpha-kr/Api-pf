<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
          'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public function projets()
    {
        return $this->belongsToMany('App\project')->withPivot('Role');
    }
    public function meetings()
    {
        return $this->belongsToMany('App\meetings');
    }
    public function tasks()
    {
        return $this->belongsToMany('App\task');
    }
    public function tokens()
    {
        return $this->hasMany('App\tokenUser','user');
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
