<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'sender_id',
        'name',
        'email',
        'subject',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
