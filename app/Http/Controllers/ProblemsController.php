<?php

namespace App\Http\Controllers;

use App\problems;
use Illuminate\Http\Request;

class ProblemsController extends Controller
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

        $validacion=\Validator::make($json,
        [
            'title'=> 'required|string',
            'description'=>'required |string',
            'project'=>'required|exists:projects,id',

        ]);
        if (!$validacion->fails()) {
            $problems=new problems($json);
            $problems->save();
            return response()->json(['status'=>'succes','code'=>201,'message'=>'problema  creado','problema'=>$problems]);

        }else{
            $res=array(
                'status'=>"error",
                'code'=>400,
                'messege'=> "Projecto no creado",
                'mistakes'=>$validacion->errors()
            );
            return \response()->json($res,400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\problems  $problems
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
     if (!empty($id)) {
        $problem=problems::find($id);
        if (!empty($problem)) {
            return \response()->json($problem,200);
        }
     }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\problems  $problems
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $json=json_decode(json_encode($request->all()) ,true);

        $validacion=\Validator::make($json,
        [   'id'=>'required|integer|exists:problems,id',
            'title'=> 'required|string',
            'description'=>'required |string',

        ]);
        if (!$validacion->fails()) {
            $problem=problems::find($json['id']);

            $problem->title=$json['title'];
            $problem->description=$json['description'];
            $problem->save();
            return response()->json(['status'=>'succes','code'=>200,'message'=>'problema  actualizado','problema'=>$problem]);


        }else{
            $res=array(
                'status'=>"error",
                'code'=>400,
                'messege'=> "Projecto no creado",
                'mistakes'=>$validacion->errors()
            );
            return \response()->json($res,400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\problems  $problems
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!empty($id)) {
            $problem=problems::find($id);
            if (!empty($problem)) {
                $problem->delete();

                return \response()->json(['status'=>'succes','code'=>200,'message'=>'problema  eliminado']);
            }
         }
    }
}
