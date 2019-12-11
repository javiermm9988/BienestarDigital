<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        
        $data = ['email' => $request->email];

        $user = user::where($data)->first();

        if ($user == NULL) 
        {
            return response()->json([
                'message' => 'unauthorized'
            ], 401);  
        }
                                
        if ($user->password == $request->password)
        {
            $token = new Token($data);
            $token = $token->encode();

            return response()->json([
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'unauthorized'
        ], 401);        

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*
        $users = User::all();
        
        return response()->json([
            $users
        ], 200);
        */
        $request_token = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($request_token);

        $user_email = $decoded_token->email;
        $user = User::where('email', '=', $user_email)->first();

        $user_categories = $user->categories;
        $category = Category::where('name', '=', $user_categories)->first();
        
        $category_passwords = $category->passwords;

        return response()->json([
            $user_categories, $user_passwords
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $requested_email = ['email' => $request->email];
        $email = user::where($requested_email)->first();

        if ($email != NULL)
        {
            return response()->json([
                "message" => 'Este email ya esta registrado'
            ], 401);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        $token = new token(['email' => $user->email]);
        $token_info = $token->encode();

        return response()->json([
            "token" => $token_info
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $request_token = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($request_token);

        $user_email = $decoded_token->email;
        $user = User::where('email', '=', $user_email)->first();
        $user_id = $user->id;

        if($user_id!=$id)
        {
            return response()->json([
                "message" => 'Solo puedes editar tu usuario'
            ], 401);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        return response()->json([
            "message" => 'Usuario actualizado'
        ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
    }
}
