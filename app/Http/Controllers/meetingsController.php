<?php

namespace App\Http\Controllers;
use App\meetings;
use Illuminate\Http\Request;
use App\project;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;

use App\User;

class meetingsController extends Controller
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
        $json= json_decode(json_encode($request->all()),true );
        $validacion=\Validator::make($json,
        [
            'Name'=> 'required|string',
            'descripcion'=> 'required|string',
            'Project_id'=>'required| exists:projects,id',
            'StartDate'=>'required|date |date_format:Y-m-d H:i:s',
            'EndDate'=>'required|date |date_format:Y-m-d H:i:s|after:StartDate'

        ]);

            if ($validacion->fails()) {
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "reunion no creada",
                    'mistakes'=>$validacion->errors()

                );
                return response()->json($res,400);

            }else{
                $meeting= new meetings($json);
                if ($meeting->save()) {
                  return response()->json(['status'=>'succes','code'=>201,'id'=>$meeting->id,'message'=>'reunion  creada']);

                }
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id=null)
    {
        if (empty($id)) {
            return meetings::all();
         }
         $meeting=meetings::find($id);
        if (!empty($meeting)) {
            return $meeting;
        }else{
         $res=array(
             'status'=>"error",
             'code'=>400,
             'messege'=> "reunion  no encontrado",
             'mistakes'=>'Id invalido'
         );
         return \response()->json($res,400);
        }
    }
    public function meetings_pro($id)
    {
       if (!empty($id)) {
          if (!empty($m=project::find($id)->meetings) ){
              return \response()->json($m,200);
          }
       }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $json= json_decode(json_encode($request->all()),true );
        $validacion=\Validator::make($json,
        [    'id'=>'required|exists:meetings,id',
            'Name'=> 'required|string',
            'descripcion'=> 'required|string',
            'StartDate'=>'date|date_format:Y-m-d H:i:s',
            'EndDate'=>'date|date_format:Y-m-d H:i:s|after:StartDate'

        ]);

            if ($validacion->fails()) {
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "reunion  no actualizado",
                    'mistakes'=>$validacion->errors()

                );
                return response()->json($res,400);

            }else{
                $sprint= meetings::find($json['id']);
                $sprint->Name=$json['Name'];
                $sprint->descripcion=$json['descripcion'];
                $sprint->StartDate=$json['StartDate'];
                $sprint->EndDate=$json['EndDate'];
                if ($sprint->save()) {

                  return response()->json(['status'=>'succes','code'=>201,'message'=>'reunion  actualizada']);

                }
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sprint=meetings::find($id);
        if (!empty($sprint)) {
             $sprint->delete();


         $res=array(
             'status'=>"succes",
             'code'=>200,
             'messege'=> "reunion   eliminada",

         );
         return \response()->json($res,200);
        }else{
         $res=array(
             'status'=>"error",
             'code'=>400,
             'messege'=> "reunion   no encontrado",
             'mistakes'=>'Id invalido'
         );
         return \response()->json($res,400);
        }
    }
    public function add_users(Request $request)
    {
        $json=json_decode(json_encode($request->all()),true );
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [

                    'user_id' => 'required|exists:users,id|integer',
                    'meetings_id'=>'required|exists:meetings,id|integer'

                ]);
            if (!$vali->fails()) {
                $meeting=meetings::find($json['meetings_id']);
                $pro=$meeting->project;
                $user=$pro->user()->where('id',$json['user_id'])->first();
                if (!empty($user)) {
                    foreach ($user->tokens as $usertoken) {
                        \app\helpers\NotificationFB::enviar("Te agregaron a una reunion","Tu equipo de  $pro->Name fecha:$meeting->StartDate","https://retos-directivos.eae.es/wp-content/uploads/2017/07/iStock-603992138-e1501191253921.jpg",$usertoken->token);
                    }

                    $user->meetings()->attach($meeting->id);
                    $res=array(
                        'status'=>"succes",
                        'code'=>200,
                        'messege'=> "reunion   agregada",

                    );
                    return \response()->json($res,200);
                }else{
                    $res=array(
                        'status'=>"error",
                        'code'=>400,
                        'messege'=> "usuario no pertenece a ese proyecto",
                        'mistakes'=>$vali->errors()

                    );
                    return \response()->json($res,400);

                }

            }else{
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "usuario no agregado a reunion",
                    'mistakes'=>$vali->errors()

                );
                return \response()->json($res,400);
            }
            }
    }
    public function showUsers($id=null)
    {
       if (!empty($id)) {
           $m=meetings::find($id);
           return \response()->json($m->users,200);
       }
    }
    public function delete_user($idu,$idm  )
    {
        $json=['user_id' =>$idu,'meetings_id'=>$idm];
        if (!empty($json)) {
            $datos = array_map('trim', $json);
            $vali = \Validator::make($datos, [

                    'user_id' => 'required|exists:users,id|integer',
                    'meetings_id'=>'required|exists:meetings,id|integer'

                ]);
            if (!$vali->fails()) {
                $meeting=meetings::find($json['meetings_id']);
                $pro=$meeting->project;

                $user=$pro->user()->where('id',$json['user_id'])->first();
                if (!empty($user)) {

                    $user->meetings()->detach($meeting->id);
                    $res=array(
                        'status'=>"succes",
                        'code'=>200,
                        'messege'=> "usuario eliminado de reunion",

                    );
                    return \response()->json($res,200);
                }else{
                    $res=array(
                        'status'=>"error",
                        'code'=>400,
                        'messege'=> "usuario no pertenece a ese proyecto",
                        'mistakes'=>$vali->errors()

                    );
                    return \response()->json($res,400);

                }

            }else{
                $res=array(
                    'status'=>"error",
                    'code'=>400,
                    'messege'=> "usuario no agregado a reunion",
                    'mistakes'=>$vali->errors()

                );
                return \response()->json($res,400);
            }
            }

    }
    public function enviar($title,$body,$img=null, $token)
{



    $firebase  = (new Factory)->withServiceAccount(__DIR__.'/firebasekey.json');
    $notification = Notification::fromArray([
        'title' => $title,
        'body' => $body,
        'image' => $img
    ]);
    $config = AndroidConfig::fromArray([
        'ttl' => '3600s',
        'priority' => 'normal',
        'notification' => [
            'title' => $title,
        'body' => $body,
        'image' => $img,
            'color' => '#0D0F74',
        ],
    ]);
    $messaging = $firebase->createMessaging();
    $message = CloudMessage::withTarget('token',$token)
        ->withNotification($notification)
        ->withData(['score' => '1.0']);
        $message= $message->withAndroidConfig($config);
        $messaging->send($message);
}
}
