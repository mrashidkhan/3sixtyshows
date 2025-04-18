<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
class AdminController extends Controller
{

    public function login(){

        // return view('login');
        return redirect()->route('user_login');
    }
    public function makeLogin(Request $request){
        $data=array(
            'email' => $request->email,
            'password' =>$request->password,
            'role' =>'admin'
        );
        if(auth::attempt($data)){
            return redirect()->route('admin.dashboard');
        }
        else{
            return back()->withErrors(['message'=>'Invalid email or password']);
        }
    }
    public function dashboard(){

        return view('admin.dashboard');
    }


    public function logout(){
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
