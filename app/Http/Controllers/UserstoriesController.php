<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\userstory;
use App\project;


class UserstoriesController extends Controller
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
            [    'ProjectID'=>' required|integer|exists:projects,id',
                'Name'=> 'required|string',
                'Description'=>'required |string',
                                 
            ]);
           
                if ($validacion->fails()) {
                    $res=array(
                        'status'=>"error",
                        'code'=>400,
                        'messege'=> "Projecto no creado",
                        'mistakes'=>$validacion->errors()
                    ); 
                    return response()->json($res,$res['code']);
                }else{
                    $id=$json['ProjectID'];
                    $datos=['Name'=>trim( $json['Name']),'Description'=> trim($json['Description'])];
                    $pro=project::find($id);
                    $story = new userstory ($datos);
                    $pro->userstories()->save($story);
                    
                    return response()->json(['status'=>'succes','code'=>200,'message'=>'historia de usuario creada']);
             
                }
       
      
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $projecto=project::find($id);
       if (!empty($projecto)) {
          return response()->json($projecto->userstories,200);
       }else{
        $res=array(
            'status'=>"error",
            'code'=>400,
            'messege'=> "Projecto con ese id no existe",
            'mistakes'=>["Error"=>"Usuario no pertenece a ese proyecto"]
        ); 
       return response()->json($res,$res['code']);
    
    }
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request )
    {
        
        $json=json_decode(json_encode($request->all()) ,true);
        

        $res=array('code'=>400, 'message'=>"Error Json");
        if (!empty($json)) {
            $validacion=\Validator::make($json,
            [    'id'=>' required|integer|exists:userstory,id',
                    'ProjectID'=>'required|integer|exists:projects,id',
                'Name'=> 'required|string',
                'Description'=>'required |string',
                                 
            ]);
           
                if ($validacion->fails()) {
                    $res=array(
                        'status'=>"error",
                        'code'=>400,
                        'messege'=> "Projecto no creado",
                        'mistakes'=>$validacion->errors()
                    ); 
                    return response()->json($res,$res['code']);
                }
                $projecto=project::find($json['ProjectID']);
                 
                  
                if (!empty($projecto)) {
                    $story=$projecto->userstories()->where('id',$json['id'])->first();
                    if (!empty($story)) {
                         $story->Name=$json['Name'];
                         $story->Description=$json['Description'];
                         $story->save();
                         return response()->json(['status'=>'succes','code'=>200,'message'=>'historia de usuario actualizado',' historia actualizada'=>$story]);

                    }else{
                        $res=array(
                            'status'=>"error",
                            'code'=>400,
                            'messege'=> "historia de usuario no encontrada",
                            'mistakes'=> ["Error"=>"Id de historia de usuario no realacionda con id proyecto"]
                        ); 
                        return response()->json($res,$res['code']); 
                    }

                    return response()->json($projecto->userstories,200);
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
    $story=userstory::find($id);
    if (!empty($story)) {
       $story->delete();
       return response()->json(['status'=>'succes','code'=>200,'message'=>'historia de usuario eliminado',' historia eliminada'=>$story]);
   

    }else{
        $res=array(
            'status'=>"error",
            'code'=>400,
            'messege'=> "not found",
            'mistakes'=>["Error"=>"historia de usuario  con ese id no existe"]
        ); 
        return response()->json($res,$res['code']);
    }
    }
}
