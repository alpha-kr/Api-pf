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
       
        $datos=array_map('trim',$json);
       
        $validacion=\Validator::make($datos,
        [    
            'Name'=> 'required|string',
            'Des'=>'required',
            'StartDate'=>'date |date_format:Y-m-d',
            'EndDate'=>'date |date_format:Y-m-d|after:start_date'
            
        ]);
            if ($validacion->fails()) {
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "Projecto no creado",
                    'mistakes'=>$validacion->errors()
                ); 
                 
            }else{
                
                $pro=null;
                $jwt=new \JWTauth();
                $iduser=$jwt->checktoken($request->header('Authorization'),true) ;
                 
                $user=User::find($iduser->user_id);
                $pro=project::create($datos) ;
                $user->projets()->attach($pro->id,['Role'=>1]);
                  $res=array(
                        'status'=>"OK",
                        'code'=>201,
                        'messege'=> "Projecto  creado",
                        'projecto'=>$pro
                        
                    ); 
                     
                   
               
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
        if (!empty($id)  ) {
            $pro=project::find($id);
            return response()->json($pro->user,200);
         }
    }
    public function addUser(Request $request)
    {
        $json=json_decode(json_encode($request->all()),true );
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [
                    'id_user' => 'required|exists:users,id|integer',
                    'id_project' => 'required|exists:projects,id|integer',
                    'id_role'=>'required|exists:roles,id|integer'
    
                ]);
            if ($vali->fails()) {
                return response()->json([$vali->errors()],400);
            }else{
                $user=User::find($datos['id_user']);
                
                
                $pro=Project::find($datos['id_project']);
                $role=Roles::find($datos['id_role']);
                
               
                $user->projets()->attach($pro->id,['Role'=>$datos['id_role']] );
                return response()->json(["status"=>"succes" ,"message"=>"usuario:{$user->email} agregado a proyecto:{$pro->Name} con role:{$role->Nombre} "],201);
            }
        }
        
        


    }
    public function Deleteuser(  Request $request)
    {
        $json=json_decode(json_encode($request->all()),true );
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [
                    'id_user' => 'required|exists:users,id|integer',
                    'id_project' => 'required|exists:projects,id|integer',
                    
    
                ]);
            if ($vali->fails()) {
                return response()->json([$vali->errors()],400);
            }else{
                $user=User::find($datos['id_user']);
                
                
                $proname= $user->projets()->where('id', $datos['id_project'])->first()->Name ;
               
                $user->projets()->detach($datos['id_project']  );
                return response()->json(["status"=>"succes" ,"message"=>"usuario:{$user->email} borrado de proyecto:{$proname}"],201);
            }
        }
    }
    public function updateuser(Request $request )
    {
        $json=json_decode(json_encode($request->all()),true );
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [
                    'id_user' => 'required|exists:users,id|integer',
                    'id_project' => 'required|exists:projects,id|integer',
                    'id_role'=>'required|exists:roles,id|integer'
    
                ]);
            if ($vali->fails()) {
                return response()->json([$vali->errors()],400);
            }else{
                $user=User::find($datos['id_user']);
                
                
                $pro= $user->projets()->where('id', $datos['id_project'])->first() ;
                $user->projets()->updateExistingPivot($pro, array('Role' => $datos['id_role']), false);
                $role=Roles::find($datos['id_role']);
                
                return response()->json(["status"=>"succes" ,"message"=>"usuario{$user->email} actualiazdo  a role: {$role->Nombre} "],201);
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
