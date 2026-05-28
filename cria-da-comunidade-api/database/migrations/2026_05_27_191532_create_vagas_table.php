<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vagas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anunciante_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->string('titulo');
            $table->string('empresa');
            $table->text('descricao')->nullable();
            $table->string('local')->nullable();
            $table->string('tipo'); // CLT, Freela, Diária, Divulgação
            $table->string('salario')->nullable();
            $table->string('salario_periodo')->nullable(); // "por mês", "por dia"
            $table->string('logo_cor', 7)->default('#FFD23F');
            $table->string('logo_iniciais', 5)->nullable();
            $table->boolean('urgente')->default(false);
            $table->integer('candidatos')->default(0);
            $table->boolean('ativa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vagas');
    }
};
