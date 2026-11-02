<?php

namespace App\Http\Controllers;

use App\Models\ResourceItem;
use App\Models\Scenario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapacityCalculatorController extends Controller
{
    public function index()
    {
        $scenario = Scenario::with('items')->firstOrCreate(
            ['is_active' => true],
            [
                'name' => 'Produksi Dashcam Mandiri',
                'description' => 'Simulasi kapasitas server & utilisasi.',
                'cpu_threshold' => 80,
                'mem_threshold' => 80,
                'disk_threshold' => 80,
                'buffer_percent' => 2,
                'growth_enabled' => true,
                'growth_unit' => 'month',
                'growth_months' => 1,
            ]
        );

        $scenarios = Scenario::all();

        return view('calculator', compact('scenario', 'scenarios'));
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'scenario_id' => 'required|exists:scenarios,id',
            'cpu_threshold' => 'required|numeric|min:1|max:100',
            'mem_threshold' => 'required|numeric|min:1|max:100',
            'disk_threshold' => 'required|numeric|min:1|max:100',
            'buffer_percent' => 'required|numeric|min:0|max:50',
            'growth_enabled' => 'required|boolean',
            'growth_unit' => 'nullable|string|in:day,month,year',
            'growth_months' => 'required|integer|min:1|max:365',
            'manual_items' => 'present|array',
            'threshold_items' => 'present|array',
        ]);

        DB::beginTransaction();
        try {
            $scenario = Scenario::findOrFail($validated['scenario_id']);
            $scenario->update([
                'cpu_threshold' => $validated['cpu_threshold'],
                'mem_threshold' => $validated['mem_threshold'],
                'disk_threshold' => $validated['disk_threshold'],
                'buffer_percent' => $validated['buffer_percent'],
                'growth_enabled' => $validated['growth_enabled'],
                'growth_unit' => $validated['growth_unit'] ?? 'month',
                'growth_months' => $validated['growth_months'],
            ]);

            // Replace items for this scenario
            ResourceItem::where('scenario_id', $scenario->id)->delete();

            $order = 1;
            foreach ($validated['manual_items'] as $item) {
                ResourceItem::create([
                    'scenario_id' => $scenario->id,
                    'mode' => 'manual',
                    'name' => $item['name'] ?? 'Item Baru',
                    'type' => strtolower($item['type'] ?? 'cpu'),
                    'spec_value' => floatval($item['spec_value'] ?? 1),
                    'spec_unit' => $item['spec_unit'] ?? ($item['type'] === 'cpu' ? 'Core' : 'GB'),
                    'current_util_percent' => floatval($item['current_util_percent'] ?? 0),
                    'target_util_percent' => isset($item['target_util_percent']) ? floatval($item['target_util_percent']) : 60,
                    'growth_est_percent' => floatval($item['growth_est_percent'] ?? 0),
                    'add_resource' => floatval($item['add_resource'] ?? 0),
                    'order' => $order++,
                ]);
            }

            foreach ($validated['threshold_items'] as $item) {
                ResourceItem::create([
                    'scenario_id' => $scenario->id,
                    'mode' => 'threshold',
                    'name' => $item['name'] ?? 'Item Baru',
                    'type' => strtolower($item['type'] ?? 'cpu'),
                    'spec_value' => floatval($item['spec_value'] ?? 1),
                    'spec_unit' => $item['spec_unit'] ?? ($item['type'] === 'cpu' ? 'Core' : 'GB'),
                    'current_util_percent' => floatval($item['current_util_percent'] ?? 0),
                    'target_util_percent' => isset($item['target_util_percent']) ? floatval($item['target_util_percent']) : null,
                    'growth_est_percent' => floatval($item['growth_est_percent'] ?? 0),
                    'add_resource' => floatval($item['add_resource'] ?? 0),
                    'order' => $order++,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data simulasi kapasitas berhasil disimpan ke database lokal!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reset()
    {
        ResourceItem::truncate();
        Scenario::truncate();

        $seeder = new \Database\Seeders\DatabaseSeeder();
        $seeder->run();

        return redirect()->route('calculator.index')->with('success', 'Data berhasil direset ke nilai standar screenshot!');
    }
}
