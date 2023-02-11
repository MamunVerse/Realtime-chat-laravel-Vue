<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

   public function user_list(){
    $user = User::latest()->get();
    return response()->json($user, 200);
   }
}
