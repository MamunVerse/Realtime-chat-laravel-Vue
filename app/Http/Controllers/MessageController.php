<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Message;
use App\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        if(!\Request::ajax()){
            return  abort(404);
        }
    }

   public function user_list(){

    $user = User::latest()->where('id', '!=', auth()->user()->id)->get();
    return response()->json($user, 200);

   }

   public function usermessage($id){

    $user = User::findOrFail($id);


    $messages = $this->message_by_user_id($id);


    return response()->json([
        'messages' => $messages,
        'user' => $user
    ], 200);

   }

   public function sendmessage(Request $request){


        $message = Message::create([
            'message' => $request->message,
            'from' => auth()->user()->id,
            'to' => $request->user_id,
            'type' => 0
        ]);

        $message = Message::create([
            'message' => $request->message,
            'from' => auth()->user()->id,
            'to' => $request->user_id,
            'type' => 1
        ]);

        broadcast(new MessageSend($message));

        return response()->json($message, 201);

   }

   public function deletesinglemessage($id){
    Message::findOrFail($id)->delete();

    return response()->json('deleted', 200);
   }

   public function deleteallemessage($id){


    $messages = $this->message_by_user_id($id);

    foreach($messages as $message){
        Message::findOrFail($message->id)->delete();
    }
    return response()->json('all deleted', 200);
   }

   public function message_by_user_id($id){
        $messages = Message::where(function($q) use ($id){
            $q->where('from', auth()->user()->id);
            $q->where('to', $id);
            $q->where('type', 0);
        })
        ->orWhere(function($q) use ($id){
            $q->where('from', $id);
            $q->where('to', auth()->user()->id);
            $q->where('type', 1);
        })
        ->with('user')
        ->get();

        return  $messages;
   }
}
