<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->morphs('avaliavel');           // avaliavel_type + avaliavel_id
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('nota');   // 1 a 5
            $table->text('texto')->nullable();
            $table->timestamps();

            // Um usuário avalia cada item somente uma vez
            $table->unique(['user_id', 'avaliavel_type', 'avaliavel_id'], 'unique_user_avaliacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
