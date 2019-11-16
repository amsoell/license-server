<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'machine_id',
    ];

    protected $primaryKey = 'machine_id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function checkins()
    {
        return $this->hasMany(Checkin::class, 'machine_id');
    }

    public function getIsValidAttribute()
    {
        return ! $this->invalidated_at;
    }
}
