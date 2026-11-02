<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'mode',
        'name',
        'type',
        'spec_value',
        'spec_unit',
        'current_util_percent',
        'target_util_percent',
        'growth_est_percent',
        'add_resource',
        'order',
    ];

    protected $casts = [
        'spec_value' => 'float',
        'current_util_percent' => 'float',
        'target_util_percent' => 'float',
        'growth_est_percent' => 'float',
        'add_resource' => 'float',
        'order' => 'integer',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }
}
