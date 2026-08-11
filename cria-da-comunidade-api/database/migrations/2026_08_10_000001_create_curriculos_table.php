<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('comunidade_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nome');
            $table->string('email');
            $table->string('telefone')->nullable();
            $table->string('area_atuacao');
            $table->json('habilidades')->nullable();
            $table->text('experiencia')->nullable();
            $table->string('cidade')->nullable();
            $table->string('disponibilidade')->default('imediata');
            $table->string('pdf_path')->nullable();
            $table->boolean('publicado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculos');
    }
};
