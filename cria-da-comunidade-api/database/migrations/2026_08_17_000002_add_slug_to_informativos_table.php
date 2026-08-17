<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Informativo;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informativos', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('titulo');
        });

        // Backfill slugs for existing records
        Informativo::all()->each(function ($inf) {
            $base = Str::slug($inf->titulo);
            $slug = $base;
            $i = 1;
            while (Informativo::where('slug', $slug)->where('id', '!=', $inf->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $inf->updateQuietly(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('informativos', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
