<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\project;

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


        $json = json_decode(json_encode($request->all()), true);
        $res = array('code' => 400, 'message' => "Error json");
        if (!empty($json)) {

            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [
                'email' => 'required|unique:users,email|email',
                'password' => 'required'

            ]);
            if ($vali->fails()) {
                $res = array(
                    'status' => "error",
                    'code' => 400,
                    'message' => "Usuario no creado",
                    'mistakes' => $vali->errors()
                );
            } else {
                $user = new User();
                $user->email = $datos['email'];
                $user->password = hash('sha256', $datos['password']);

                if ($user->save()) {
                    $res = array(
                        'status' => "OK",
                        'code' => 201,
                        'message' => "user  creado",
                        'user' => $user

                    );
                }
            }
        }

        return response()->json($res, $res['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $jwt = new \JWTauth();
        $token=$jwt->checktoken($request->header('Authorization'), true);
        $id  = (!empty($token) )?$token->user_id:null;
        if (!empty($id)) {
           $usuario = User::find($id);

        $projects =  $usuario->projets ;
        return response()->json($projects, 200);  
        }else{
            return response()->json(['error'=>'token invalido'], 400);
        }
       
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
        $res = array('code' => 400, 'message' => "Error json");
        $datos=json_decode(json_encode( $request->all()),true);
        $datos=array_merge($datos,["id"=>$id]);

     
        
        $val=\Validator::make(  $datos  ,["id"=>"required|integer|exists:projects,id", "Name"=> "required| string", "Des"=>"required|string",'StartDate'=>'date','EndDate'=>'date |after:start_date'] );
        if ($val->fails()) {
            $res = array(
                'status' => "error",
                'code' => 400,  
                'mistakes' => $val->errors()
            );
        }else{
            $jwt = new \JWTauth();

            $token=$jwt->checktoken($request->header('Authorization'), true);
            $id_user = (!empty($token) )?$token->user_id:null;
           
            if (!empty($id_user) ) {
                
                $usuario = User::find($id_user);
                $pro= $usuario->projets()->where('id', $id)->first();
                $role =$pro->pivot->Role;
                if ($role == "Scrum Master") {
                    $pro->Name=$datos['name'];
               $pro->Des=$datos['des'];
               $pro->StartDate=(isset($datos['StartDate']))?$datos['StartDate']:null;
               $pro->EndDate=(isset($datos['EndDate']))?$datos['EndDate']:null;
               if ($pro->save()) {
                   $res=array(
                       "status"=>"succes",
                       "code"=>200,
                       "message"=>"proyecto actualizado",
                       "proyecto"=>$pro
                   );
               }else{
                $res = array(
                    'status' => "Error",
                    'code' => 400,
                    'messege' => "Error base de datos",
                     
                );
               }
                }
               

            }else{

                $res = array(
                    'status' => "Error",
                    'code' => 400,
                    'messege' => "No tienes permiso para eliminar",
                     
                );
            }
        }
        return response()->json($res,$res['code']);
    }
    public function login(Request $request)
    {
        $json = json_decode(json_encode($request->all()), true);

        $res = array('code' => 400, 'message' => "Error json");
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [
                'email' => 'required|email',
                'password' => 'required'

            ]);
            if ($vali->fails()) {
                $res = array(
                    'status' => "error",
                    'code' => 400,
                    'messege' => "El login ha fallado",
                    'mistakes' => $vali->errors()
                );
            } else {

                $pswd = hash('sha256', $datos['password']);
                $jwt = new \JWTauth();
                $token=$jwt->getToken($datos['email'], $pswd);
                $res = array(
                     
                    "code" => (isset($token['code']))?400:200,
                    "token" => $token

                );
                if (!empty($datos['gettoken'])) {
                    $res = array(
                         
                        "code" => 200,
                        "token" => $jwt->getToken($datos['email'], $pswd, true)

                    );
                }
            }
        }

        return response()->json($res, $res['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $res = array('code' => 400, 'message' => "Error json");
        $val=\Validator::make( ["id"=>$id],["id"=>"required|integer|exists:projects,id"] );
        if ($val->fails()) {
            $res = array(
                'status' => "error",
                'code' => 400,
                'messege' => "Projecto con ese id no existe",
                'mistakes' => $val->errors()
            );
        }else{
            $jwt = new \JWTauth();

            $token=$jwt->checktoken($request->header('Authorization'), true);
            $id_user = (!empty($token) )?$token->user_id:null;
           
            if (!empty($id_user) ) {
                
                $usuario = User::find($id_user);
                $pro= $usuario->projets()->where('id', $id)->first();
                $role =$pro->pivot->Role;
                if ($role == "Scrum Master")  
                $usuario->projets()->detach($id);
    
                if ( $pro->delete()) {
                    $res = array(
                        'status' => "succes",
                        'code' => 200,
                        'messege' => "Projecto   eliminado",
                        'proyecto' => $pro
                    );
                }
            }else{
                $res = array(
                    'status' => "Error",
                    'code' => 400,
                    'messege' => "No tienes permiso para eliminar",
                     
                );
            }
        }
        

        
        








        return response()->json($res, $res['code']);
    }

    public function All()
    {
        $usuarios=User::all();
        return response()->json($usuarios,200);
    }
}
