<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $json=json_decode($request->input('json',null),true);
        $datos=array_map('trim',$json);
        $vali=\Validator::make($datos,[
            'email'=>'required|unique:users,email|email',
            'password'=>'required'

        ]);
        if ($vali->fails()) {
            $res=array(
                'status'=>"error",
                'code'=>400,
                'messege'=> "Usuario no creado",
                'mistakes'=>$vali->errors()
            ); 
        }else{
            $user = new User();
            $user->email=$datos['email'];
            $user->password=hash('sha256',$datos['password']);
            
            if ($user->save()) {
                $res=array(
                    'status'=>"OK",
                    'code'=>201,
                    'messege'=> "user  creado",
                    'user'=>$user
                    
                ); 
            }

        }
        return response()->json($res,$res['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $jwt= new \JWTauth();
        $id=$jwt->checktoken($request->header('Authorization'),true)->user_id;
        $usuario= User::find($id);
        
        $projects=array(
            'status'=>'OK',
            'code'=>'200',
            'proyectos'=> $usuario->projets

        
        );
        return response()->json($projects,200);
         
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    public function login(Request $request)
    {
        $json=json_decode($request->input('json',null),true);
        $datos=array_map('trim',$json);
        $vali=\Validator::make($datos,[
            'email'=>'required|email',
            'password'=>'required'

        ]);
        if ($vali->fails()) {
            $res=array(
                'status'=>"error",
                'code'=>400,
                'messege'=> "El login ha fallado",
                'mistakes'=>$vali->errors()
            ); 
        }else{

            $pswd=hash('sha256',$datos['password']);
            $jwt=new \JWTauth();
            $res=$jwt->getToken($datos['email'],$pswd);
            if (!empty($datos['gettoken'])) {
                $res=$jwt->getToken($datos['email'],$pswd,true);
            }
             


        }

       return response()->json($res,200);
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
