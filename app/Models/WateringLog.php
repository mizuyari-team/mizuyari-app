<?php

namespace App\Models;

use Database\Factories\WateringLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WateringLog extends Model
{
    /** @use HasFactory<WateringLogFactory> */
    use HasFactory;

    protected $fillable = ['plant_id', 'watered_at', 'amount_ml', 'memo', 'photo_path'];

    protected $casts = ['watered_at' => 'datetime'];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
