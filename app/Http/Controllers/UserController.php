<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(Request $request)
    {
        //creating validator
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return array(
                'error' => true,
                'message' => $validator->errors()->all()
            );
        }

        $user = new User();

        $user->username = $request->input('username');
        $user->password = (new BcryptHasher)->make($request->input('password'));

        $user->save();

        unset($user->password);

        return response()->json(['error' => false, 'user' => $user]);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => true, 
                'message' => $validator->errors()->all()
            ]);
        }

        $user = User::where('username', $request->input('username'))->first();

        if(count(array($user))){
            if(password_verify($request->input('password'), $user->password)){
                unset($user->password);
                return response()->json(['error' => false, 'user'=>$user]);
            } else{
                return response()->json(['error' => true, 'message'=>'Invalid Password']);
            }
        } else {
            return response()->json(['error' => true, 'message'=>'User not Exist']);
        }
    }
}
