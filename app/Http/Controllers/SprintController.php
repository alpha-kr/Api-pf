<?php

namespace App\Http\Controllers;

use App\sprint;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class SprintController extends Controller
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
          'Project_id'=>'required| exists:projects,id',
          'StartDate'=>'required|date |date_format:Y-m-d',
          'EndDate'=>'required|date |date_format:Y-m-d|after:StartDate'
          
      ]);
     
          if ($validacion->fails()) {
              $res=array(
                  'status'=>"error",
                  'code'=>400,
                  'messege'=> "Sprint  no creado",
                  'mistakes'=>$validacion->errors()
                  
              ); 
              return response()->json($res,400);
               
          }else{
              $sprint= new sprint($json);
              if ($sprint->save()) {
                 
                return response()->json(['status'=>'succes','code'=>201,'message'=>'sprint  creada']);

              }
          }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function show($id=null)
    {
        if (empty($id)) {
           return sprint::all();
        }
        $sprint=sprint::find($id);
       if (!empty($sprint)) {
           return $sprint->with('tasks')->get();
       }else{
        $res=array(
            'status'=>"error",
            'code'=>400,
            'messege'=> "Sprint  no encontrado",
            'mistakes'=>'Id invalido'
        );
        return \response()->json($res,400);
       }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request )
    {
        $json= json_decode(json_encode($request->all()),true );
      $validacion=\Validator::make($json,
      [    'id'=>'required|exists:sprints,id',
          'Name'=> 'required|string',
          'StartDate'=>'date|date_format:Y-m-d',
          'EndDate'=>'date|date_format:Y-m-d|after:StartDate'
          
      ]);
     
          if ($validacion->fails()) {
              $res=array(
                  'status'=>"error",
                  'code'=>400,
                  'messege'=> "Sprint  no actualizado",
                  'mistakes'=>$validacion->errors()
                  
              ); 
              return response()->json($res,400);
               
          }else{
              $sprint= sprint::find($json['id']);
              $sprint->Name=$json['Name'];
              $sprint->StartDate=$json['StartDate'];
              $sprint->EndDate=$json['EndDate'];
              if ($sprint->save()) { 
                 
                return response()->json(['status'=>'succes','code'=>201,'message'=>'sprint  actualizada']);

              }
          }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sprint=sprint::find($id);
       if (!empty($sprint)) {
            $sprint->delete();


        $res=array(
            'status'=>"succes",
            'code'=>200,
            'messege'=> "Sprint   eliminado",
          
        );
        return \response()->json($res,200);
       }else{
        $res=array(
            'status'=>"error",
            'code'=>400,
            'messege'=> "Sprint  no encontrado",
            'mistakes'=>'Id invalido'
        );
        return \response()->json($res,400);
       }
    }
}
