<?php

namespace App\Http\Controllers;

use App\task;
use App\project;
use Illuminate\Http\Request;

class TaskController extends Controller
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
                'Description'=>'required |string',
                'Status'=>'required |integer |exists:status,id',
                'ProjectID'=>' required|integer|exists:projects,id',
                'Sprint_id'=>'integer|exists:sprints,id',
                'UserStoryID'=>'integer|exists:userstory,id',
                
                                 
            ]);
            if ($validacion->fails()) {
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "tarea no creada",
                    'mistakes'=>$validacion->errors()
                ); 
                return response()->json($res,400);
            }else{ 
                $task= new task($json);
                
                if ($task->save()) {
                    return response()->json(['status'=>'succes','code'=>201,'message'=>'tarea  creada']);

                }
               

            }
        }

         
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\task  $task
     * @return \Illuminate\Http\Response
     */
    public function show_project_task($id)
    {
       if (!empty($id)) {
          $pro=project::find($id);
       
          if (!empty($pro)) {
              
            return response()->json($pro->tasks,200);

          }
       } 
    }
    public function show($id=null)
    {
        if (!empty($id)) {
            $task=task::find($id);
            return response()->json($task,200);
         }else{
            $task=task::all();
            return response()->json($task,200);

         }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $json=json_decode(json_encode($request->all()) ,true);
        $res=array('code'=>400, 'message'=>"Error Json");
        if (!empty($json)) {
            $validacion=\Validator::make($json,
            [    'id'=>'required |integer |exists:tasks,id',
                'Name'=> 'required|string',
                'Description'=>'required |string',
                'Status'=>'required |integer |exists:status,id',
                 
                'Sprint_id'=>'integer|exists:sprints,id',
                'UserStoryID'=>'integer|exists:userstory,id',
                
                                 
            ]);
            if ($validacion->fails()) {
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "Tarea no actualizada",
                    'mistakes'=>$validacion->errors()
                ); 
                return response()->json($res,400);
            }else{ 
                $task=  task::find($json['id']);
                if (!empty($task) ){
                    $task->Name=$json['Name'];
                    $task->Description=$json['Description'];
                    $task->Status=$json['Status'];
                    $task->Sprint_id=(isset($json['Sprint_id']))?$json['Sprint_id']:null;
                    $task->UserStoryID=(isset($json['UserStoryID']))?$json['UserStoryID']:null;
                    
                }
                if ($task->save()) {
                    return response()->json(['status'=>'succes','code'=>200,'message'=>'tarea  actualizada']);

                }
               

            }
        }

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res=array(
            'status'=>"error",
            'code'=>400,
            'messege'=> "Tarea no Eliminada",
            'mistakes'=>["Error"=>"tarea  con id {$id} no existe"]
        );
        if (!empty($id)) {
            $task=task::find($id);
            if (!empty($task)) {
                 $task->delete();
                 return response()->json(['status'=>'succes','code'=>200,'message'=>'tarea  eliminada']);

            }
            return response()->json($res,$res['code']);

         } 
        
    }
}
