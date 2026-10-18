<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pré-cadastro rápido de profissionais (link público compartilhado pelo dono da plataforma).
        // Fica separado de "profissionais": o perfil só vai ao portal depois que o admin aprova.
        Schema::create('cadastros_profissionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->string('nome', 100);
            $table->string('whatsapp', 20)->unique();          // só dígitos, DDD + número (sem +55)
            $table->string('email', 150)->nullable();
            $table->string('categoria', 40);                    // área da plataforma (Beleza, Construção...)
            $table->string('profissao', 80);                    // o que a pessoa faz (Eletricista, Barbeiro...)
            $table->text('descricao')->nullable();
            $table->boolean('atende_domicilio')->nullable();    // null = não se aplica à profissão
            $table->boolean('tem_site')->default(false);
            $table->boolean('tem_google_meu_negocio')->default(false);
            $table->string('nivel_digital', 12);                // basico | avancado
            $table->string('status', 20)->default('novo')->index(); // novo | contatado | aprovado | descartado
            $table->foreignId('profissional_id')->nullable()->constrained('profissionais')->nullOnDelete();
            $table->string('origem', 60)->nullable();           // ?ref= do link (quem enviou)
            $table->text('observacao_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadastros_profissionais');
    }
};
