<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrocinadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nome');
            $table->string('imagem_url', 500)->nullable();
            $table->text('texto')->nullable();
            $table->string('link_url', 500)->nullable();
            $table->string('texto_botao', 60)->default('Saiba mais');
            $table->boolean('ativo')->default(false);
            $table->dateTime('publicado_em')->nullable();
            $table->dateTime('expira_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrocinadores');
    }
};
