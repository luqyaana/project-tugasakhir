<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saran_pencegahans', function (Blueprint $table) {
            $table->string('kode_kategori')->unique()->after('id');
            $table->string('nama_kategori')->after('kode_kategori');
            $table->string('rentang_label')->after('nama_kategori');
            $table->unsignedInteger('batas_bawah')->after('rentang_label');
            $table->unsignedInteger('batas_atas')->nullable()->after('batas_bawah');
            $table->string('warna_badge')->nullable()->after('batas_atas');
            $table->string('warna_teks')->nullable()->after('warna_badge');
            $table->text('saran_pencegahan')->after('warna_teks');
        });
    }

    public function down(): void
    {
        Schema::table('saran_pencegahans', function (Blueprint $table) {
            $table->dropUnique(['kode_kategori']);
            $table->dropColumn([
                'kode_kategori',
                'nama_kategori',
                'rentang_label',
                'batas_bawah',
                'batas_atas',
                'warna_badge',
                'warna_teks',
                'saran_pencegahan',
            ]);
        });
    }
};
