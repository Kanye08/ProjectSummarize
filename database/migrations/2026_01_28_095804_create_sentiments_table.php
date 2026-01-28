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
        Schema::create('sentiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->enum('overall_sentiment', ['positive', 'negative', 'neutral', 'mixed']);
            $table->decimal('positive_score', 5, 2)->default(0);
            $table->decimal('negative_score', 5, 2)->default(0);
            $table->decimal('neutral_score', 5, 2)->default(0);
            $table->json('sentiment_breakdown')->nullable();
            $table->json('chart_data')->nullable();
            $table->timestamps();
            
            $table->index('meeting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentiments');
    }
};
