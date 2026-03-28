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
        'reply',
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
    // ─── Accessors ───────────────────────────────────────────────────────────────
    public function getStarArrayAttribute()
    {
        // تقريب الرقم لأقرب نصف نجمة
        $rating = round(floatval($this->rating) * 2) / 2;

        // نجوم كاملة
        $full = floor($rating);

        // نصف نجمة إذا الفرق 0.5
        $half = ($rating - $full) === 0.5 ? 1 : 0;

        // النجوم الفارغة، لا يمكن أن تكون أقل من 0 أو أكثر من 5
        $empty = max(0, 5 - $full - $half);

        return [
            'full'  => min(5, $full),
            'half'  => $half,
            'empty' => $empty,
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────
    // Scope للفلترة حسب الاسم
    public function scopeUsername($query, $username)
    {
        if ($username) {
            $query->whereHas('user', function ($q) use ($username) {
                $q->where('name', 'like', '%' . $username . '%');
            });
        }
    }

    // Scope للفلترة حسب النجوم
    public function scopeRating($query, $star)
    {
        if ($star) {
            $query->where('rating', $star);
        }
    }

    // Scope للفلترة حسب الكلمة في التعليق
    public function scopeComment($query, $comment)
    {
        if ($comment) {
            $query->where('comment', 'like', '%' . $comment . '%');
        }
    }

    // Scope للفلترة حسب التواريخ
    public function scopeDateFrom($query, $date_from)
    {
        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }
    }

    public function scopeDateTo($query, $date_to)
    {
        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }
    }
}
