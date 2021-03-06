<?php

namespace App\Http\Controllers;
use App\User;

use App\project;
use App\Roles;
use Illuminate\Http\Request;

class ProjectController extends Controller
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
        $json=json_decode(json_encode($request->all()) ,true);
        $res=array('code'=>400, 'message'=>"Error Json");
        if (!empty($json)) {



        $validacion=\Validator::make($json,
        [
            'Name'=> 'required|string',
            'Des'=>'required |string',
            'StartDate'=>'nullable|date |date_format:Y-m-d',
            'EndDate'=>'nullable|date |date_format:Y-m-d|after:StartDate'

        ]);

            if ($validacion->fails()) {
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "Projecto no creado",
                    'mistakes'=>$validacion->errors()
                );

            }else{
                $datos=['Name'=>trim( $json['Name']),'Des'=> trim($json['Des']),"StartDate"=>(isset($json['StartDate']))?$json['StartDate']:null,"EndDate"=>(isset($json['EndDate']))?$json['EndDate']:null];
                $pro=null;
                $jwt=new \JWTauth();
                $iduser=$jwt->checktoken($request->header('Authorization'),true) ;
                 if (!empty($iduser)) {

                    $user=User::find($iduser->user_id);
                $pro=project::create($datos) ;
                $user->projets()->attach($pro->id,['Role'=>1]);
                  $res=array(
                        'status'=>"OK",
                        'code'=>201,
                        'messege'=> "Projecto  creado",
                        'projecto'=>$pro

                    );
                 }else{

                    $res=array(
                        'status'=>"error",
                        'code'=>400,
                        'messege'=> "Projecto no creado",
                        'mistakes'=> "error de token"
                    );
                 }




            }
        }
        return response()->json($res,$res['code']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if (!empty($id)  ) {
           $pro=project::find($id);
           return response()->json($pro,200);
        }
    }
    public function userproject($id)
    {
        if (!empty($id)) {
            $pro=project::find($id);
            return response()->json($pro->user,200);
         }
    }
    public function projectProblem($id)
    {
        if (!empty($id)) {
            $pro=project::find($id);
            if (!empty($pro)) {
                return response()->json($pro->problems,200);
            }

         }
    }
    public function addUser(Request $request)
    {
        $json=json_decode(json_encode($request->all()),true );
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [
                    'email' => 'required|exists:users,email|email',
                    'id_project' => 'required|exists:projects,id|integer',
                    'id_role'=>'required|exists:roles,id|integer'

                ]);
            if ($vali->fails()) {
                return response()->json([ 'status'=>"error",
                'code'=>400,
                'messege'=> "fallo",
                'mistakes'=>  $vali->errors()],400);
            }else{
                $user=User::where('email', $datos['email'])->first();
                $pro=Project::find($datos['id_project']);
                $role=Roles::find($datos['id_role']);


                $user->projets()->attach($pro->id,['Role'=>$datos['id_role']] );
                try {
                    foreach ($user->tokens as $usertoken) {
                        \app\helpers\NotificationFB::enviar("Tienes un nuevo proyecto","Fuiste agregado al proyecto:{$pro->Name} ",'https://blog.wearedrew.co/hubfs/metodolog%C3%ADa%20scrum.png',$usertoken->token);
                        }
                } catch (\Throwable $th) {
                    //throw $th;
                }

                return response()->json(["status"=>"succes" ,"message"=>"usuario:{$user->email} agregado a proyecto:{$pro->Name} con role:{$role->Nombre} "],201);
            }
        }




    }
    public function Deleteuser($id,$proj )
    {
        $d=["id_user"=>$id,"id_project"=>$proj];

        if (!empty($d['id_user']) && !empty($d['id_project']) ) {

            $datos=$d;
            $vali = \Validator::make($datos, [
                'id_user' => 'required|exists:users,id|integer',
                    'id_project' => 'required|exists:projects,id|integer',


                ]);
            if ($vali->fails()) {
                return response()->json([ 'status'=>"error",
                'code'=>400,
                'messege'=> "fallo",
                'mistakes'=>  $vali->errors()],400);
            }else{
                $user=User::find( $datos['id_user']);


                $pro= $user->projets()->where('id', $datos['id_project'])->first()  ;

               if (!empty($pro)) {
                foreach($pro->tasks as $task)
                $user->tasks()->detach($task->id);
                $user->projets()->detach($datos['id_project']  );

                return response()->json(["status"=>"succes" ,"message"=>"usuario:{$user->email} borrado de proyecto:{$pro->Name}"],201);

               }else{
                return response()->json([ 'status'=>"error",
                'code'=>400,
                'messege'=> "fallo",
                'mistakes'=> ["Error"=>"Usuario no pertenece a ese proyecto"] ],400);

               }
                      }
        }
    }
    public function showsprint($id)
    {
        $pro=project::find($id);
        if (!empty($pro)) {
            return response()->json($pro->sprints,200);
        }
    }
    public function updateuser(Request $request )
    {
        $json=json_decode(json_encode($request->all()),true );
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [
                    'email' => 'required|exists:users,email|email',
                    'id_project' => 'required|exists:projects,id|integer',
                    'id_role'=>'required|exists:roles,id|integer'

                ]);
            if ($vali->fails()) {
                return response()->json([ 'status'=>"error",
                'code'=>400,
                'messege'=> "fallo",
                'mistakes'=>  $vali->errors()],400);
            }else{
                $user=User::where('email', $datos['email'])->first();


                $pro= $user->projets()->where('id', $datos['id_project'])->first() ;
                if (!empty($pro)) {
                    $user->projets()->updateExistingPivot($pro, array('Role' => $datos['id_role']), false);
                $role=Roles::find($datos['id_role']);

                return response()->json(["status"=>"succes" ,"message"=>"usuario:{$user->email} actualiazdo  a role:{$role->Nombre} "],201);

                }else{
                    return response()->json([ 'status'=>"error",
                    'code'=>400,
                    'messege'=> "fallo",
                    'mistakes'=> ["Error"=>"Usuario no pertenece a ese proyecto"] ],400);


                }
                           }
        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, project $project)
    {
        //
    }
    public function users($id)
    {
        $pro=project::find($id);
        if (!empty($pro)) {
             return response()->json($pro->user,200);
        }else{
            $res=[
                'status'=>"error",
                'code'=>400,
                'messege'=> "proyecto invalido",
                'mistakes'=> "id de proyecto no existe"
            ];
            return response()->json($res,400);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(project $project)
    {

    }
}
