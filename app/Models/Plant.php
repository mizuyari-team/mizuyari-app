<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    /** @use HasFactory<\Database\Factories\PlantFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'species', 'location', 'watering_interval_days', 'memo'];

public function user() { return $this->belongsTo(User::class); }
public function wateringLogs() { return $this->hasMany(WateringLog::class); }
}

