<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('model_arimas', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('diproses_pada');
        });

        $latestModelId = DB::table('model_arimas')
            ->orderByDesc('diproses_pada')
            ->orderByDesc('id')
            ->value('id');

        if ($latestModelId !== null) {
            DB::table('model_arimas')
                ->where('id', $latestModelId)
                ->update([
                    'is_published' => true,
                    'published_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('model_arimas', function (Blueprint $table) {
            $table->dropColumn([
                'is_published',
                'published_at',
            ]);
        });
    }
};
