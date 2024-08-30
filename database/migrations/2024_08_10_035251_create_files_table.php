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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->enum('disk', ['public', 'local']);
            $table->string('name_to_save');
            $table->string('original_name');
            $table->date('upload_date');
            $table->integer('size');
            $table->string('description')->nullable();
            $table->enum('security_status', ['safe', 'doubtful', 'malicious']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
