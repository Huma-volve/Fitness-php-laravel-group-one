<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        return view('login.login');
    }
    public function verifaylogin(Request $request){
    $request->validate([
        'email'=>'required|email',
        'password'=>'required'
    ]);
    if(Auth::attempt([
        'email'=>$request->email,
        'password'=>$request->password
    ])){


            if(Auth::user()->role=='admin' ){
                return redirect()->route('admin.index');
            }
            elseif (Auth::user()->role=='trainer'){
                return redirect()->route('home');
            }else
            {
                    Auth::logout();
                    return back()->with('error','You are not authorized');
            }
    }  else{
          return back()->with('error', 'Email or password is wrong');
    }

    }
     public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
