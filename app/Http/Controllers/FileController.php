<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        //
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
}
