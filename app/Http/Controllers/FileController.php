<?php

namespace App\Http\Controllers;
use App\meetings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\file;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
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
                'id_reunion'=>'required|exists:meetings,id|integer|unique:files,id_reunion',
                'files'=>'array'

            ]
        );
        if ($val->fails()) {
            $res = array(
                'status' => "error",
                'code' => 400,
                'messege' => "acta no creada",
                'mistakes' => $val->errors()
            );
            return response()->json($res, 400);
        }else{
           $file= $json['files'];

                $filename =  Str::random(5).''.$file[0]['extension'];
                if ($archivo=base64_decode($file[0]['base64'])) {
                    \File::put(storage_path(). '/app/' . $filename, $archivo);

                    $dbfile= new file(['ruta'=>  $filename ,'id_reunion'=>$json['id_reunion']]);
                     $dbfile->save();
                }



               $res = array(
                'status' => "OK",
                'code' => 201,
                'messege' => "acta creada",
                'archivo' => $dbfile->id

            );
            return \response()->json($res, 200);
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

        $file=file::find($id);
        if (!empty($file)) {


            $archivo=Storage::get($file->ruta);
             $res=['name'=>$file->ruta ,'base64'=>base64_encode($archivo)];

            return response()->json($res,200);

        }
    }
    public function showacta($id,$has)
    {

        if (!empty($id)) {
            $file=file::where('id_reunion',$id)->first();
            if (!empty($file) &&  $has=='false') {


                $archivo=Storage::get($file->ruta);
                 $res=['name'=>$file->ruta ,'base64'=>base64_encode($archivo)];

                return response()->json($res,200);

            }else{
                if ($has=='true' && !empty($file)   ) {
                    return response()->json(["response"=>true],200);
                }else{
                    if ($has=='true' && empty($file)) {
                        return response()->json(["response"=>false],200);
                    }
                }
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        $file=file::find($id);
        if (!empty($file)) {

            Storage::delete($file->ruta);
            $file->delete();
            $res = array(
                'status' => "OK",
                'code' => 201,
                'messege' => "Archivo   eliminado",


            );
            return \response()->json($res,200);
        }
    }
    public function destroyActa($id)
    {


        $file=meetings::find($id);
        if (!empty($file)) {
            $file=file::where('id_reunion',$id)->first();
            if (!empty($file)) {
                Storage::delete($file->ruta);
                $file->delete();
                $res = array(
                    'status' => "OK",
                    'code' => 201,
                    'messege' => "Archivo   eliminado",


                );
                return \response()->json($res,200);
            }

        }
    }
}
