<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'imports' table.
 *
 * This table stores records of data import processes, including their status,
 * progress, scheduling information, and any associated metadata or errors.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method creates the 'imports' table with the defined schema.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->string('description')->nullable();
            $table->integer('processed_items')->default(0);
            $table->integer('total_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method drops the 'imports' table if it exists.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imports');
    }
};