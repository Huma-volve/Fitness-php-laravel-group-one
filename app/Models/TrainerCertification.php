<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerCertification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'trainer_id',
        'certificate_name',
        'organization',
        'year',
        'path',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
