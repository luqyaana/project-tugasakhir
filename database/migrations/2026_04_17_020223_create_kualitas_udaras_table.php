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
        Schema::create('kualitas_udaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained('datasets')->cascadeOnDelete();
            $table->timestamp('waktu_pencatatan');
            $table->decimal('nilai_no2', 8, 2);
            $table->string('jenis_data', 20)->default('real');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kualitas_udaras');
    }
};
