<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainers()
    {
        return $this->belongsToMany(Trainer::class, 'trainer_specializations');
    }

    public function searchHistory()
    {
        return $this->hasMany(SearchHistory::class);
    }
}
