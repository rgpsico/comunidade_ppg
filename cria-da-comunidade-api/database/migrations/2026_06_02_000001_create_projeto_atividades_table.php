<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projeto_atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained()->cascadeOnDelete();
            $table->string('titulo', 120);
            $table->string('dias', 80)->default('');      // "Seg, Qua e Sex"
            $table->string('horario', 40)->default('');   // "07:00 – 08:00"
            $table->text('descricao')->nullable();
            $table->unsignedSmallInteger('vagas')->nullable();
            $table->unsignedTinyInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projeto_atividades');
    }
};
