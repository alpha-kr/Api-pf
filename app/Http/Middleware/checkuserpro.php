<?php

namespace App\Http\Middleware;

use Closure;

class checkuserpro
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token=$request->header('Authorization');
        $json = json_decode(json_encode($request->all()), true);
        $pro=(isset($json['id']))?$json['id']:null;
        $sw= new \JWTauth();
        
        $data=array(
            "status"=>"No autorizado",
            "code"=>"400",
            "messege"=>"No estas autorizado para esta funcionalidad"
             
        );
        
        if ( !$sw->checktoken($token,$pro) && empty($token)    ) {
            return response()->json($data,$data['code']);
        }else{
            
            return $next($request);
        }
    }
}
