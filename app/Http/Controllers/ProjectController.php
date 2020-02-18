<?php

namespace App\Http\Controllers;
use App\User;

use App\project;
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
        $json=json_decode($request->input('json',null),true);
       
        $datos=array_map('trim',$json);
       
        $validacion=\Validator::make($datos,
        [    
            'Name'=> 'required|alpha',
            'Des'=>'required',
            'StartDate'=>'date',
            'EndDate'=>'date |after:start_date'
            
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
                $user->projets()->attach($pro->id,['Role'=>'Scrum Master']);
                  $res=array(
                        'status'=>"OK",
                        'code'=>201,
                        'messege'=> "Projecto  creado",
                        'projecto'=>$pro
                        
                    ); 
                     
                   
               
            }
        return response()->json($res,$res['code']);
              
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(project $project)
    {
        //
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
        //
    }
}
