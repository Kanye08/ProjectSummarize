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
        Schema::table('meetings', function (Blueprint $table) {
            Schema::table('meetings', function (Blueprint $table) {
            $table->string('audio_file_path')->nullable()->after('status');
            $table->string('audio_file_name')->nullable()->after('audio_file_path');
            $table->bigInteger('audio_file_size')->nullable()->after('audio_file_name');
            $table->integer('duration')->nullable()->after('audio_file_size'); // in seconds
            $table->string('audio_format')->nullable()->after('duration');
            $table->enum('processing_status', [
                'pending',
                'uploaded',
                'processing',
                'transcribing',
                'transcribed',
                'summarizing',
                'completed',
                'failed'
            ])->default('pending')->after('audio_format');
            $table->text('error_message')->nullable()->after('processing_status');
            $table->json('metadata')->nullable()->after('error_message');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn([
                'audio_file_path',
                'audio_file_name',
                'audio_file_size',
                'duration',
                'audio_format',
                'processing_status',
                'error_message',
                'metadata'
            ]);
        });
    }
};
