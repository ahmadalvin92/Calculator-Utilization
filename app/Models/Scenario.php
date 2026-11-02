<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'cpu_threshold',
        'mem_threshold',
        'disk_threshold',
        'buffer_percent',
        'growth_enabled',
        'growth_unit',
        'growth_months',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'growth_enabled' => 'boolean',
        'cpu_threshold' => 'float',
        'mem_threshold' => 'float',
        'disk_threshold' => 'float',
        'buffer_percent' => 'float',
        'growth_months' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(ResourceItem::class)->orderBy('order')->orderBy('id');
    }

    public function manualItems()
    {
        return $this->hasMany(ResourceItem::class)->where('mode', 'manual')->orderBy('order')->orderBy('id');
    }

    public function thresholdItems()
    {
        return $this->hasMany(ResourceItem::class)->where('mode', 'threshold')->orderBy('order')->orderBy('id');
    }
}
