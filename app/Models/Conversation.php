<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'trainer_id',
        'status',
        'last_message_at',
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

 
    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

 
    public function unreadCount()
    {
        return $this->messages()->where('status', '!=', 'read')->count();
    }
}