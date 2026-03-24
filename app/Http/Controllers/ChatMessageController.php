<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    public function startConversation(Request $request){
        $user=Auth::user()->id;
        $valdiated=$request->validate([
            'trainer_id'=>'required|exists:users,id'
        ]);
        $conversation=Conversation::firstOrCreate([
            'user_id'=>$user,
            'trainer_id'=>$valdiated['trainer_id']
        ],[
            'status'=>'active'
        ]
        );
        return response()->json([
            'message'=>'chat ',
            'Conversation'=>$conversation
        ],200);

    }

    public function getConversations(Request $request){
         $user=Auth::user()->id;
         $myConversation=Conversation::where('user_id','=',$user)->orwhere('trainer_id','=',$user)
         ->with(['lastMessage','trainer','user'])->withCount(['messages as unread_count'=>function($query){
            $query->where('status','!=','read')->where('sender_id','!=', Auth::user()->id);
         }])->orderByDesc('last_message_at')->get();
           return response()->json([
            
            'Conversation'=>$myConversation
        ],200);
    }
    public function getMessages($id){
        $Conversation=Conversation::find($id);

if (!$Conversation) {
    return response()->json(['message' => 'Conversation not found'], 404);
}
        if($Conversation->user_id !=Auth::user()->id && $Conversation->trainer_id !=Auth::user()->id){
              return response()->json(['message' => 'Unauthorized'], 403);
        }
           $messages =$Conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->paginate(50);

        return response()->json($messages);
    }

public function sendMessage(Request $request, $id)
{
    $userId = auth()->id();
    if (!$userId) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $body = $request->input('body'); 

    if (!$body || strlen($body) > 3000) {
        return response()->json([
            'errors' => ['body' => 'This field is required and must be a string max 3000 characters']
        ], 422);
    }

   $conversation = Conversation::find($id);

if (!$conversation) {
    return response()->json(['message' => 'Conversation not found'], 404);
}
    if ($conversation->user_id != $userId && $conversation->trainer_id != $userId) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $message = ChatMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $userId,
        'body' => $body,
        'status'=> 'sent',
    ]);

    broadcast(new MessageSent($message))->toOthers();

    $conversation->update(['last_message_at' => now()]);

    return response()->json($message, 201);
}

public function markAsRead($conversationId)
    {
        $conversation = Conversation::find($conversationId);
       
if (!$conversation) {
    return response()->json(['message' => 'Conversation not found'], 404);
}
        if ($conversation->user_id !== Auth::user()->id && $conversation->trainer_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', Auth::user()->id)
            ->where('status', '!=', 'read')
            ->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);

        return response()->json(['message' => 'Messages marked as read']);
    }



}
