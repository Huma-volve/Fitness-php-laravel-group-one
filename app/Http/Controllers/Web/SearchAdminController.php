<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class SearchAdminController extends Controller
{
     public function index(){
        
        $users =  User:: select("name", "email" ,"id" ,"role")->get();
        return view('dashboard.admin.search' ,compact('users') );

        
    }

    public function searchText(Request $request)
    {
        $q = $request->search_text;

       
            $data = User::where('name', 'like', "%$q%")
            ->orWhere('email', 'like', "%$q%")
            ->orWhere('role', 'like', "%$q%")
            
            ->get();

            return response()->json($data);
    }
    public function searchInfo( $id)
    {
        

       
            $user = User::findOrFail($id);
           

           return view('dashboard.admin.searchUser', compact('user') );
    }
}
