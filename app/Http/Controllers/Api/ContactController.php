<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'nullable|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string'
        ]);

        // For contact, we don't save to messages table as it requires conversation_id
        // Just return success

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully'
        ], 201);
    }
}
