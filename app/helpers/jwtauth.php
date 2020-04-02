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
    public function getToken($u,$p,$token=null )
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
                $data=$data=["status"=>"succes","code"=>200,"id_user"=>$user->id, "token"=>$Gtoken];
            }else{
                $data=["status"=>"succes","code"=>200,  "usuario"=>$Dtoken];
              
            }
        }else{
            $data=array(
                'status'=>"error",
                'code'=>401,
                'messege'=> "No se puedo logear",
                'mistakes'=>["Error"=>["error en credenciales"]]
                 
            ); 

        }

      
        


        return $data;
    }
    public function checktoken($token, $getIdentity=false,$pro=null)
    {
     
        $valido=false;
        try {
            $token=str_replace('"','',$token);
            $Dtoken=JWT::decode($token,$this->key,['HS256'] );
            if (!empty($Dtoken) && is_object($Dtoken) && isset($Dtoken->user_id)) {
                $u=User::find($Dtoken->user_id);
                if (!empty($pro) && is_integer($pro)) {
                    $proj=$u->projects()->where('id', $id)->first();
                    if (empty($proj)) {
                         return false;
                    }else{
                        return true;
                    }
                }else{
                    $valido=true;
                }
                
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