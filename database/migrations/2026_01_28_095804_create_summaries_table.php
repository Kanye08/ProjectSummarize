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
         Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->text('summary_text');
            $table->text('brief_summary')->nullable();
            $table->json('action_points')->nullable();
            $table->json('key_decisions')->nullable();
            $table->json('key_topics')->nullable();
            $table->json('participants_mentioned')->nullable();
            $table->timestamps();
            
            $table->index('meeting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};
