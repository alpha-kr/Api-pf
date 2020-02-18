<?php

namespace App\Http\Middleware;

use Closure;

class checkuser
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
      
         
        $sw= new \JWTauth();
        
        $data=array(
            "status"=>"No autorizado",
            "code"=>"400",
            "messege"=>"Necesitas ser usuario para acceder a esa funcionalidad"
             
        );
        
        if ( !$sw->checktoken($token) && empty($token)    ) {
            return response()->json($data,$data['code']);
        }else{
            
            return $next($request);
        }
        
    }
}
