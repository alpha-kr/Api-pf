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
        $id = $jwt->checktoken($request->header('Authorization'), true)->user_id;
        $usuario = User::find($id);

        $projects = array($usuario->projets);
        return response()->json($projects, 200);
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

                $res = array(
                    "status" => "succes",
                    "code" => 200,
                    "token" => $jwt->getToken($datos['email'], $pswd)

                );
                if (!empty($datos['gettoken'])) {
                    $res = array(
                        "status" => "succes",
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
            $id_user = $jwt->checktoken($request->header('Authorization'), true)->user_id;
            $usuario = User::find($id_user);
            $pro= $usuario->projets()->where('id', $id)->first();
            $role =$pro->pivot->Role;
            if ($role == "Scrum Master") {
                $usuario->projets()->detach($id);
    
                if ( $pro->delete()) {
                    $res = array(
                        'status' => "succes",
                        'code' => 200,
                        'messege' => "Projecto   eliminado",
                        'proyecto' => $pro
                    );
                }
            }
        }
        

        
        








        return response()->json($res, $res['code']);
    }
}
