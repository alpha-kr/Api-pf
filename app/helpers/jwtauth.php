<?php
namespace App\helpers;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;


class jwtauth{

public  $key;

public function __construct()
{
    $this->key="mi clave del token es esta";
} 
    public function getToken($u,$p,$token=null)
    {

        $user=User::where(['email'=>$u,'password'=>$p])->first();
        
        if (is_object($user)) {
            $toke=array(
                'user_id'=>$user->id,
                'email'=>$user->email,
                'started_at'=> time(),
                 'exp'=> time()+(60*60*4)
            );
            $Gtoken= JWT::encode($toke,$this->key,'HS256');
            $Dtoken=JWT::decode($Gtoken,$this->key,['HS256'] );

            if (is_null($token)) {
                $data=$Gtoken;
            }else{
                $data=$Dtoken;
            }
        }else{
            $data=array(
                'status'=>"error",
                'code'=>400,
                'messege'=> "Usuario incorrecto",
                 
            ); 

        }

      
        


        return $data;
    }
    public function checktoken($token, $getIdentity=false)
    {
     
        $valido=false;
        try {
            $token=str_replace('"','',$token);
            $Dtoken=JWT::decode($token,$this->key,['HS256'] );
            if (!empty($Dtoken) && is_object($Dtoken) && isset($Dtoken->user_id)) {
                $valido=true;
             if ($getIdentity) {
                 $valido=$Dtoken;
             }
             }
            
 
        } catch (\UnexpectedValueException $e) {
           $valido=false;
        }
        catch(\DomainException $e){
            $valido=false;
        }
        
       
        
        return $valido;

    }
}



?>