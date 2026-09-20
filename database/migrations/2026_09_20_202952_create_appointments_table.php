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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->unsignedInteger('duration_minutes');
            $table->foreignId('project_id')->index()->constrained()->restrictOnDelete();
            $table->string('project_task')->nullable();
            $table->string('occurrence')->nullable();
            $table->text('internal_description')->nullable();
            $table->string('entry_type', 20);
            $table->string('owner')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
