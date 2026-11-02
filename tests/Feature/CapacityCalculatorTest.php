<?php

namespace Tests\Feature;

use App\Models\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapacityCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculator_page_renders_with_scenario(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Kalkulator Utilisasi');
        $response->assertSee('Simulation by Threshold');
    }

    public function test_sync_api_updates_thresholds_and_items(): void
    {
        $scenario = Scenario::create([
            'name' => 'Test Scenario',
            'cpu_threshold' => 80,
            'mem_threshold' => 80,
            'disk_threshold' => 80,
            'buffer_percent' => 2,
            'growth_enabled' => true,
            'growth_months' => 1,
        ]);

        $payload = [
            'scenario_id' => $scenario->id,
            'cpu_threshold' => 75,
            'mem_threshold' => 80,
            'disk_threshold' => 85,
            'buffer_percent' => 5,
            'growth_enabled' => true,
            'growth_months' => 3,
            'manual_items' => [
                [
                    'name' => 'Web App',
                    'type' => 'cpu',
                    'spec_value' => 16,
                    'spec_unit' => 'Core',
                    'current_util_percent' => 60,
                    'target_util_percent' => 60,
                    'growth_est_percent' => 0,
                    'add_resource' => 0,
                ]
            ],
            'threshold_items' => [
                [
                    'name' => 'Database Primary',
                    'type' => 'memory',
                    'spec_value' => 32,
                    'spec_unit' => 'GB',
                    'current_util_percent' => 90,
                    'target_util_percent' => 50,
                    'growth_est_percent' => 20,
                    'add_resource' => 16,
                ]
            ],
        ];

        $response = $this->postJson('/api/sync', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('scenarios', [
            'id' => $scenario->id,
            'cpu_threshold' => 75,
            'buffer_percent' => 5,
        ]);

        $this->assertDatabaseHas('resource_items', [
            'scenario_id' => $scenario->id,
            'name' => 'Database Primary',
            'add_resource' => 16,
        ]);
    }
}
