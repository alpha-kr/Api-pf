<?php

namespace App\Http\Controllers;

use App\tokenUser;
use Illuminate\Http\Request;

class TokenUserController extends Controller
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
        $json=json_decode(json_encode($request->All()),true);
    $val=\Validator::make($json,['user'=>'required|integer| exists:users,id','token'=>'required']);
    if (!$val->fails()) {
        $usertoken= new tokenUser($json);
        $usertoken->save();
        $res=array(
            'status'=>"succes",
            'code'=>201,
            'messege'=> "usuario y token guardado",

        );
        return \response()->json($res,201);

    }else{
        $res=array(
            'status'=>"error",
            'code'=>400,
            'messege'=> "usuario no  agregado",
            'mistakes'=>$val->errors()

        );
        return \response()->json($res,400);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\tokenUser  $tokenUser
     * @return \Illuminate\Http\Response
     */
    public function show(tokenUser $tokenUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\tokenUser  $tokenUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, tokenUser $tokenUser)
    {
        $json=json_decode(json_encode($request->All()),true);
        $val=\Validator::make($json,['user'=>'required|integer| exists:token-user,user','token'=>'required']);
        if (!$val->fails()) {
            $usertoken= tokenUser::where('user',$json['user'])->first();
            $usertoken->token=$json['token'];
            $usertoken->save();
            $res=array(
                'status'=>"succes",
                'code'=>201,
                'messege'=> "usuario y token actualizado",

            );
            return \response()->json($res,201);
    }else{
        $res=array(
            'status'=>"error",
            'code'=>400,
            'messege'=> "usuario no  agregado",
            'mistakes'=>$val->errors()

        );
        return \response()->json($res,400);
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\tokenUser  $tokenUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(tokenUser $tokenUser)
    {
        //
    }
}
