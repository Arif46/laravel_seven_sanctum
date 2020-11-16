<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use Hash;
use Validator;
use Auth;

class AuthController extends Controller

{
    /**
     *User Register
     */
    public function register(Request $request)
    {
           $validator=Validator::make($request->all(),[
               'name'=>'required',
               'email'=>'required|email',
               'password'=>'required',
           ]);

           if($validator->fails()){
               return response()->json([$validator->errors()],400);
           }
           $user = new User();
           $user->name =$request->name;
           $user->email=$request->email;
           $user->password=Hash::make($request->password);
           $user->save();
           return response()->json([
               'status_code'=>200,
               'message'=>'User Created Successfully'
           ]);
    }

     /**
     *User login
     */

     public function login(Request $request)
     {
        $validator=Validator::make($request->all(),[

            'email'=>'required|email',
            'password'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],400);
        }
        $credentials=request(['email','password']);
        if (!Auth::attempt($credentials))
        {
            return response()->json([
                'status_code'=>500,
                 'message'=>'Unauthorized'
            ]);

        }
        $user=User::where('email',$request->email)->first();
        $tokenResult=$user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status_code'=>200,
             'token'=>$tokenResult
        ]);

     }

      /**
     *User logout
     */

     public function logout(Request $request)
     {
         $request->user()->currentAccessToken()->delete();

         return response()->json([
            'status_code'=>200,
             'message'=>'Token Deleted Sucessfully!'
        ]);
     }
}
