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
            $table->string('fake_name')->nullable();
            $table->string('original_name');
            $table->dateTime('upload_date');
            $table->text('description')->nullable();
            $table->unsignedInteger('size');
            $table->enum('disk', ['public', 'local']);
            $table->enum('security_status', ['safe', 'doubtful', 'malicious']);
            $table->enum('visibility_status', ['public', 'private']);
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
