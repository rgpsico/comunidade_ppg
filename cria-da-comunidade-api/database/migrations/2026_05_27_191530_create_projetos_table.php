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
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('icone', 10)->default('❤');
            $table->string('causa'); // educação, esporte, cultura, assistência, saúde, música
            $table->string('cor', 7)->default('#2BD96B');
            $table->string('impacto_valor')->nullable();
            $table->string('impacto_label')->nullable();
            $table->integer('progresso')->default(0);
            $table->decimal('arrecadado', 12, 2)->default(0);
            $table->decimal('meta', 12, 2)->nullable();
            $table->string('cta_label')->default('Apoiar');
            $table->integer('anos_atuando')->default(1);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
