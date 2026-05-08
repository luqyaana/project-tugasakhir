<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('file_size');
        });

        $latestDatasetId = DB::table('datasets')
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->value('id');

        if ($latestDatasetId !== null) {
            DB::table('datasets')
                ->where('id', $latestDatasetId)
                ->update(['is_active' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
