<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'trainer_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
