<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resource_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained()->cascadeOnDelete();
            $table->string('mode')->default('manual'); // 'manual' or 'threshold'
            $table->string('name');
            $table->string('type'); // 'cpu', 'memory', 'disk'
            $table->float('spec_value'); // e.g. 16, 18, 4, 6, 1000
            $table->string('spec_unit')->default('Core'); // 'Core', 'GB', 'TB'
            $table->float('current_util_percent'); // e.g. 60, 90, 75, 95
            $table->float('target_util_percent')->nullable(); // e.g. 60, 37.5, 72.08
            $table->float('growth_est_percent')->default(0); // e.g. 1, 0, 80, 10
            $table->float('add_resource')->default(0); // recommended additional resource
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_items');
    }
};
