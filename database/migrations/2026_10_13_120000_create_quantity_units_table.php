<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateQuantityUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('quantity_units')) {
            Schema::create('quantity_units', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('level')->unique()->comment('1 = top packaging, 2 = middle packaging, 3 = base unit');
                $table->string('name');
                $table->string('description')->nullable();
                $table->boolean('is_base')->default(false)->comment('If true, this unit is the base unit and cannot be disabled');
                $table->boolean('status')->default(true)->comment('1 = enabled, 0 = disabled');
                $table->timestamps();
            });

            // Seed initial 3 levels: Cartons, Packets, Pieces
            DB::table('quantity_units')->insert([
                [
                    'level' => 1,
                    'name' => 'Cartons',
                    'description' => 'Top packaging unit (e.g. Cartons, Boxes, Crates)',
                    'is_base' => false,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'level' => 2,
                    'name' => 'Packets',
                    'description' => 'Middle packaging unit (e.g. Packets, Rolls, Bundles)',
                    'is_base' => false,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'level' => 3,
                    'name' => 'Pieces',
                    'description' => 'Base unit (e.g. Pieces, Units, Items). Locked base unit.',
                    'is_base' => true,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quantity_units');
    }
}
