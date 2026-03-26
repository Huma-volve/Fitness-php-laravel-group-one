<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    use HasFactory;
    protected $table = 'user_cards';

    protected $fillable = [
        'user_id',
        'card_brand',
        'card_last_four',
        'card_exp_month',
        'card_exp_year',
        'card_holder_name',
    ];

    protected function casts(): array
    {
        return [
            'card_exp_month' => 'integer',
            'card_exp_year'  => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        if (! $this->card_exp_year || ! $this->card_exp_month) {
            return false;
        }

        return now()->isAfter(
            now()->setYear($this->card_exp_year)->setMonth($this->card_exp_month)->endOfMonth()
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
