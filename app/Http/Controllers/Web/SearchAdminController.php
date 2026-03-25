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
        
        $User = [];
        $Booking = [];
        $Payment = [];
        return view('dashboard.admin.search', compact('User','Booking','Payment')  );

        
    }

    public function searchText(Request $request)
    {
        $q = $request->search_text;

       
            $data = User::where('name', 'like', "%$q%")
            ->orWhere('email', 'like', "%$q%")
            ->limit(5)
            ->get();

            return response()->json($data);

           
        

       

        
        

        

       

        

        return response()->json([]);
    }
}
