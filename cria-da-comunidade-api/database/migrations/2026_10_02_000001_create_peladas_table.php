<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peladas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->foreignId('organizador_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->string('tipo', 20)->default('quadra');
            $table->unsignedTinyInteger('jogadores_linha');
            $table->unsignedTinyInteger('goleiros_por_time')->default(0);
            $table->unsignedSmallInteger('tempo_jogo_min');
            $table->string('local')->nullable();
            $table->string('quando')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['comunidade_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peladas');
    }
};
