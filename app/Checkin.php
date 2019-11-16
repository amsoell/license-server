<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    protected $fillable = [
        'machine_id',
        'request',
    ];

    protected $casts = [
        'request' => 'array',
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }
}
