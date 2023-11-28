<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('module_id')->constrained();
            $table->integer('parent_task_id')->unsigned()->default(0);
            $table->string('route', 100)->unique();
            $table->string('name', 60);
            $table->string('description')->nullable();
            $table->boolean('status')->default(1)->index()->comment('0=disabled, 1=enabled');
            $table->boolean('visibility')->default(1)->comment('0=not shown in navigation, 1=shown');
            $table->integer('order')->unsigned()->default(0);
            $table->string('icon', 50)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
