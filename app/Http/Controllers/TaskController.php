<?php

namespace App\Http\Controllers;

use App\task;
use App\project;
use App\User;
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
                'priority'=>'required|integer|min:0|max:2',
                'level'=>'required|integer|min:0|max:2',
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
                    return response()->json(['status'=>'succes','code'=>201,'message'=>'tarea  creada','task'=>$task]);

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

            return response()->json($pro->tasks()->with('commentes_files')->get(),200);

          }
       }
    }
    public function show($id=null)
    {
        if (!empty($id)) {
            $task=task::with('comments')->find($id) ;
            return response()->json($task,200);
         }else{
            $task=task::with('comments')->get();
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
            [    'id'=>'required |integer|exists:tasks,id',
                'Name'=> 'required|string',
                'Description'=>'required |string',
                'Status'=>'required |integer |exists:status,id',
                'priority'=>'required|integer|min:0|max:2',
                'level'=>'required|integer|min:0|max:2',
                'Sprint_id'=>'integer|nullable|exists:sprints,id',
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
                    $task->level=$json['level'];
                    $task->priority=$json['priority'];

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
    public function addUser(Request $request)
    {
        $json=json_decode(json_encode($request->All()),true);
       $iduser=isset($json['user'])?$json['user']:null;
        $val=\Validator::make($json,[  'task'=>'required|integer|exists:tasks,id| unique:task_user,task_id,null,task_id,user_id,'.$iduser,
        'user'=>'required|integer|exists:users,id' ]);
        if (!$val->fails()) {
            $t=task::find($json['task']);
            $p=project::find($t->ProjectID);
            $user=User::find($json['user']);

            $t->users()->attach($user->id);
            try {
                foreach ($user->tokens as $usertoken) {
                    \app\helpers\NotificationFB::enviar("Tienes una nueva tarea","Fuiste agregado a la tarea {$t->Name} del proyecto {$p->Name}",'https://jipanel.com/blog/image/19/post-1529640873-image_fileuser_id_43.png',$usertoken->token);
                    }
            } catch (\Throwable $th) {
                //throw $th;
            }

            return response()->json(['status'=>'succes','code'=>200,'message'=>'usuario  agregado']);



        }else{
                $res=array(
                'status'=>"error",
                'code'=>400,
                'messege'=> "usurio no agregado",
                'mistakes'=>$val->errors()

            );
            return \response()->json($res,400);
        }
    }
    public function showUser($id=null)
    {

        $task=task::find($id);
        if (!empty($task)) {
            return \response()->json($task->users,200);

        }else{

            return \response()->json(task::all(),200);
        }

    }
    public function destroyUser($id,$iduser)
    {
        /* $task=task::find($id); */
        /* $user=$task->users()->where('id',$iduser)->first(); */
        $user=User::find($iduser);
        $task=$user->tasks()->where('id',$id)->first();
        if (!empty($task) && !empty($user)) {
             $task->users()->detach($user->id);
             return response()->json(["status"=>"succes" ,"message"=>"usuario:{$user->email} borrado de tarea:{$task->Name}"],200);
        }else{
            response()->json([ 'status'=>"error",
                'code'=>400,
                'messege'=> "fallo",
                'mistakes'=> ["Error"=>"Usuario no pertenece a esa tarea o id incorrectos en ambos casos"] ],400);
        }
    }

}
