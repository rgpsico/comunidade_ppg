<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artigos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->nullable()->constrained()->nullOnDelete();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumo')->nullable();
            $table->longText('corpo');
            $table->string('imagem_capa_url')->nullable();
            $table->string('categoria', 60)->default('Notícia');
            $table->string('autor', 120)->nullable();
            $table->boolean('publicado')->default(false);
            $table->timestamp('publicado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artigos');
    }
};
