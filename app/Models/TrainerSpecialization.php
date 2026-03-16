<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TrainerSpecialization extends Pivot
{
    public $timestamps = false;

    protected $table = 'trainer_specializations';

    protected $fillable = [
        'trainer_id',
        'specialization_id',
    ];
}
