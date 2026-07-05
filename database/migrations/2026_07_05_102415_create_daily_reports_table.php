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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('report_date');
            $table->integer('productivity_rating'); // 1 = Kurang, 2 = Cukup, 3 = Baik
            $table->integer('tasks_completed')->default(0);
            $table->text('notes'); // Kenapa hari ini baik / kemarin kurang
            $table->text('obstacles')->nullable(); // Kendala
            $table->timestamps();

            $table->unique(['workspace_id', 'user_id', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
