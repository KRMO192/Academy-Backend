<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use App\Models\User;


class RegisterController extends BaseController
{
    public function Register(Request $request )
    {
        $validator = Validator::make($request->all(),[
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
        'c_password' => 'required|same:password'
        ]);

        if($validator->fails()){
           return $this->sendError('validate error',$validator->errors());
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('tokenKey')->accessToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success , 'uesr registered successfully');
    }




    public function Login(Request $request )
    {


        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

///
            if (auth()->user()->type == 'student') {
                $user = Auth::user();
                $success['token'] = $user->createToken('tokenKey')->accessToken;
                $success['name'] = $user->name;
                $success['type'] = $user->type;

                return $this->sendResponse($success , 'student login successfully');

                return redirect()->route('students');
            }else if (auth()->user()->type == 'instructor') {
                $user = Auth::user();
                $success['token'] = $user->createToken('tokenKey')->accessToken;
                $success['name'] = $user->name;
                $success['type'] = $user->type;

                return $this->sendResponse($success , 'instructor login successfully');

                //return redirect()->route('manager.home');
            }else if (auth()->user()->type == 'admin') {
                $user = Auth::user();
                $success['token'] = $user->createToken('tokenKey')->accessToken;
                $success['name'] = $user->name;
                $success['type'] = $user->type;

                return $this->sendResponse($success , 'admin login successfully');

            }else{
                return redirect()->route('home');
            }

///
            // $user = Auth::user();
            // $success['token'] = $user->createToken('tokenKey')->accessToken;
            // $success['name'] = $user->name;

            // return $this->sendResponse($success , 'uesr login successfully');
        }
        else{
            return $this->sendError('check your email or password',['error' => 'unauthrised']);
        }

    }
}
