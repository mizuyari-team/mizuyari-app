<?php

namespace App\Models;

use Database\Factories\PlantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    /** @use HasFactory<PlantFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'species', 'location', 'pot_size',
        'daily_base_ml', 'default_interval_days', 'photo_path', 'is_demo',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wateringLogs()
    {
        return $this->hasMany(WateringLog::class);
    }
}
