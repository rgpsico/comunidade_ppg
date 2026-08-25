<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('profissional_id')->nullable()->constrained('profissionais')->nullOnDelete();
            $table->enum('tipo', ['problema', 'servico'])->default('problema');
            $table->string('titulo', 200);
            $table->text('descricao');
            $table->string('categoria', 80);
            $table->json('fotos')->nullable();
            $table->string('local', 200)->nullable();
            $table->string('estimativa_valor', 100)->nullable();
            $table->string('valor_acordado', 100)->nullable();
            $table->enum('urgencia', ['normal', 'urgente', 'critico'])->default('normal');
            $table->enum('status', ['aberto', 'aceito', 'em_andamento', 'resolvido', 'cancelado'])->default('aberto');
            $table->timestamp('aceito_em')->nullable();
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamados');
    }
};
