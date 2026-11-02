<?php

namespace Database\Seeders;

use App\Models\ResourceItem;
use App\Models\Scenario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Mandiri Production Scenario
        $scenario = Scenario::create([
            'name' => 'Produksi Dashcam Mandiri',
            'description' => 'Simulasi kapasitas server & utilisasi.',
            'is_active' => true,
            'cpu_threshold' => 80,
            'mem_threshold' => 80,
            'disk_threshold' => 80,
            'buffer_percent' => 1,
            'growth_enabled' => true,
            'growth_unit' => 'month',
            'growth_months' => 1,
        ]);

        // Manual items
        ResourceItem::create([
            'scenario_id' => $scenario->id,
            'mode' => 'manual',
            'name' => 'Aplikasi Web',
            'type' => 'cpu',
            'spec_value' => 16,
            'spec_unit' => 'Core',
            'current_util_percent' => 60,
            'target_util_percent' => 60,
            'growth_est_percent' => 0,
            'add_resource' => 0,
            'order' => 1,
        ]);

        ResourceItem::create([
            'scenario_id' => $scenario->id,
            'mode' => 'manual',
            'name' => 'API Gateway Core',
            'type' => 'memory',
            'spec_value' => 32,
            'spec_unit' => 'GB',
            'current_util_percent' => 85,
            'target_util_percent' => 70,
            'growth_est_percent' => 0,
            'add_resource' => 7,
            'order' => 2,
        ]);

        ResourceItem::create([
            'scenario_id' => $scenario->id,
            'mode' => 'manual',
            'name' => 'Storage Log Audit',
            'type' => 'disk',
            'spec_value' => 500,
            'spec_unit' => 'GB',
            'current_util_percent' => 90,
            'target_util_percent' => 75,
            'growth_est_percent' => 0,
            'add_resource' => 100,
            'order' => 3,
        ]);

        // Threshold items
        // Row 1: Faldy (CPU 18 Core, 1% util -> Aman! 0 Rec)
        ResourceItem::create([
            'scenario_id' => $scenario->id,
            'mode' => 'threshold',
            'name' => 'Faldy',
            'type' => 'cpu',
            'spec_value' => 18,
            'spec_unit' => 'Core',
            'current_util_percent' => 1,
            'target_util_percent' => 1,
            'growth_est_percent' => 1,
            'add_resource' => 0,
            'order' => 1,
        ]);

        // Row 2: Tyo (CPU 4 Core, 90% util -> Butuh 1 Core!)
        ResourceItem::create([
            'scenario_id' => $scenario->id,
            'mode' => 'threshold',
            'name' => 'Tyo',
            'type' => 'cpu',
            'spec_value' => 4,
            'spec_unit' => 'Core',
            'current_util_percent' => 90,
            'target_util_percent' => 72,
            'growth_est_percent' => 0,
            'add_resource' => 1,
            'order' => 2,
        ]);

        // Row 3: Heni (Memory 6 GB, 75% util, 80% growth -> Butuh 5 GB!)
        ResourceItem::create([
            'scenario_id' => $scenario->id,
            'mode' => 'threshold',
            'name' => 'Heni',
            'type' => 'memory',
            'spec_value' => 6,
            'spec_unit' => 'GB',
            'current_util_percent' => 75,
            'target_util_percent' => 40.91,
            'growth_est_percent' => 80,
            'add_resource' => 5,
            'order' => 3,
        ]);

        // Row 4: SSD FALDY (Disk 1000 GB, 8% util -> Aman! 0 Rec)
        ResourceItem::create([
            'scenario_id' => $scenario->id,
            'mode' => 'threshold',
            'name' => 'SSD FALDY',
            'type' => 'disk',
            'spec_value' => 1000,
            'spec_unit' => 'GB',
            'current_util_percent' => 8,
            'target_util_percent' => 8,
            'growth_est_percent' => 1,
            'add_resource' => 0,
            'order' => 4,
        ]);
    }
}
