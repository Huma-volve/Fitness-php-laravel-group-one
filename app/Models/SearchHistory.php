<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'trainer_id',
        'specialization_id',
        'search_text',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
}
