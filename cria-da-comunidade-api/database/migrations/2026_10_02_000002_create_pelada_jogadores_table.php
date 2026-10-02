<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelada_jogadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('nome_convidado')->nullable();
            $table->boolean('goleiro')->default(false);
            $table->unsignedInteger('posicao');
            $table->timestamps();

            $table->unique(['pelada_id', 'user_id']);
            $table->index(['pelada_id', 'posicao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelada_jogadores');
    }
};
