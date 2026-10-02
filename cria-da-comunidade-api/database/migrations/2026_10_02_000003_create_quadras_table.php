<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quadras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->string('nome');
            $table->string('endereco')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['comunidade_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quadras');
    }
};
