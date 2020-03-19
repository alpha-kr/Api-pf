<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\commets;
use App\file;
use Illuminate\Http\Request;

class CommentsController extends Controller
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
        $json = json_encode($request->all());
        $json = \json_decode($json, true);
       
         
        $val = \Validator::make(
            $json,
            [
                'Description' => 'required| string',
                'id_user' => 'required|exists:Users,id',
                'id_task' => 'required|integer|exists:tasks,id',
                'files'=>'array'
            
            ]
        );
        if ($val->fails()) {
            $res = array(
                'status' => "error",
                'code' => 400,
                'messege' => "comentario no creado",
                'mistakes' => $val->errors()
            );
            return response()->json($res, 400);
        } else {
            $comment = new commets();
            $comment->Description = $json['Description'];
            $comment->id_user = $json['id_user'];
            $comment->id_task = (isset($json['id_task'])) ? $json['id_task'] : null;
            $comment->save();
            if (isset($json['files'])) {
               foreach ($json['files'] as $file) { 
                $filename =  Str::random(5).''.$file['extension'];
                if ($archivo=base64_decode($file['base64'])) {
                    \File::put(storage_path(). '/app/' . $filename, $archivo);
                    
                    $dbfile= new file(['ruta'=>  $filename]);
                    $comment->files()->save($dbfile);
                }
               
               
               }

            }
          
            $res = array(
                'status' => "OK",
                'code' => 201,
                'messege' => "comentario   creado",
                'comentarios' => $comment

            );
            return \response()->json($res, 200);
        }
    }
    public function addfile(Request $request)
    {
        $json = json_encode($request->all());
        $json = \json_decode($json, true);
       
         
        $val = \Validator::make(
            $json,
            [
                'id' => 'required|exists:comments,id',
                'files'=>'required|array'
            ]
        );
        
        if ($val->fails()) {
            $res = array(
                'status' => "error",
                'code' => 400,
                'messege' => "error  archivo no subido",
                'mistakes' => $val->errors()
            );
            return response()->json($res, 400);
        }else{
            $comment=commets::find($json['id']);
            foreach ($json['files'] as $file) { 
                $filename =  Str::random(5).''.$file['extension'];
                if ($archivo=base64_decode($file['base64'])) {
                    \File::put(storage_path(). '/app/' . $filename, $archivo);
                    
                    $dbfile= new file(['ruta'=>  $filename]);
                    $comment->files()->save($dbfile);
                }
               
               
               }
               $res = array(
                'status' => "OK",
                'code' => 201,
                'messege' => "arhivo agregado a comentario {$json['id']}",
                'comentario' => $comment

            );
            return \response()->json($res, 200);

        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\commets  $commets
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        $json = json_encode($request->all());
        $json = \json_decode($json, true);
       
         
        $val = \Validator::make(
            $json,
            [
                'Description' => 'required| string',
                'id' => 'required|exists:comments,id',
                 
            
            ]
        );
        if ($val->fails()) {
            $res = array(
                'status' => "error",
                'code' => 400,
                'messege' => "comentario no actualizado",
                'mistakes' => $val->errors()
            );
            return response()->json($res, 400);
        }else{
            $comment=commets::find($json['id']);
            if (!empty($comment)) {
               $comment->Description=$json['Description'];
               $comment->save();
               $res = array(
                'status' => "OK",
                'code' => 200,
                'messege' => "comentario   actualizado",
                'comentarios' => $comment);
                return response()->json($res, $res['code']);
            }


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\commets  $commets
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
        if (!empty($id)) {
          $c= commets::find($id);
          if (!empty($c)) {

            foreach ($c->files as $file) {
                Storage::delete($file->ruta);

            }


            $c->delete();
            $res = array(
                'status' => "OK",
                'code' => 201,
                'messege' => "comentario   eliminado",
               

            );
            return response()->json($res, 200);
            
          }else{

            $res = array(
                'status' => "error",
                'code' => 400,
                'messege' => "comentario no eliminado",
                'mistakes' =>["Error"=>"comentario   con id {$id} no existe"]
            );
            return response()->json($res,400);
          }
          
        } 
    }
}
