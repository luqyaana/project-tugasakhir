<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('model_arimas', function (Blueprint $table) {
            $table->foreignId('dataset_id')->nullable()->after('id')->constrained('datasets')->nullOnDelete();
            $table->string('nama_model')->nullable()->after('dataset_id');
            $table->string('status', 30)->default('draft')->after('nama_model');
            $table->unsignedTinyInteger('p')->nullable()->after('status');
            $table->unsignedTinyInteger('d')->nullable()->after('p');
            $table->unsignedTinyInteger('q')->nullable()->after('d');
            $table->json('hasil_uji')->nullable()->after('q');
            $table->json('hasil_forecast')->nullable()->after('hasil_uji');
            $table->text('ringkasan_hasil')->nullable()->after('hasil_forecast');
            $table->timestamp('diproses_pada')->nullable()->after('ringkasan_hasil');
        });
    }

    public function down(): void
    {
        Schema::table('model_arimas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dataset_id');
            $table->dropColumn([
                'nama_model',
                'status',
                'p',
                'd',
                'q',
                'hasil_uji',
                'hasil_forecast',
                'ringkasan_hasil',
                'diproses_pada',
            ]);
        });
    }
};
