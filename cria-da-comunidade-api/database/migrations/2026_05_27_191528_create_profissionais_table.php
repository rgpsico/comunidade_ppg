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
        Schema::create('profissionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->string('nome');
            $table->string('categoria'); // Beleza, Construção, Casa, Transporte, Eventos, Saúde
            $table->string('cargo'); // ex: "Manicure · Em casa"
            $table->text('bio')->nullable();
            $table->decimal('estrelas', 2, 1)->default(5.0);
            $table->integer('total_avaliacoes')->default(0);
            $table->integer('total_atendimentos')->default(0);
            $table->decimal('preco_a_partir', 8, 2)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('cor1', 7)->default('#FF5E1A');
            $table->string('cor2', 7)->default('#FFD23F');
            $table->json('tags')->nullable();
            $table->string('tempo_resposta')->nullable();
            $table->boolean('verificado')->default(false);
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
        Schema::dropIfExists('profissionais');
    }
};
