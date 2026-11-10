<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WateringLog extends Model
{
    /** @use HasFactory<\Database\Factories\WateringLogFactory> */
    use HasFactory;

    protected $fillable = ['plant_id', 'watered_at', 'memo'];
protected $casts = ['watered_at' => 'datetime'];

public function plant() { return $this->belongsTo(Plant::class); }
}

