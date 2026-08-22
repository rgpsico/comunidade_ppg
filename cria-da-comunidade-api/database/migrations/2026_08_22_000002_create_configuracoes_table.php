<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nome_plataforma', 120)->default('Cria da Comunidade');
            $table->string('logo_url', 500)->nullable();
            $table->string('favicon_url', 500)->nullable();
            $table->string('cor_primaria', 20)->default('#FF5E1A');
            $table->string('cor_secundaria', 20)->default('#FFD23F');
            $table->string('cor_destaque', 20)->default('#2BD96B');
            $table->string('cor_fundo', 20)->default('#0D0B09');
            $table->string('cor_card', 20)->default('#1C1916');
            $table->string('cor_texto', 20)->default('#F5F0E8');
            $table->string('cor_muted', 20)->default('#8B847B');
            $table->enum('listagem_tipo', ['grade', 'lista'])->default('grade');
            $table->unsignedSmallInteger('itens_por_pagina')->default(20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes');
    }
};
