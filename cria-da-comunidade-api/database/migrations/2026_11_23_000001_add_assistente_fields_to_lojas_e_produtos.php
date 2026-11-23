<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dados que o assistente do produto usa para responder sem inventar:
 * políticas da loja (valem para todos os produtos) e detalhes opcionais de cada produto.
 * Também guarda os interessados (antes só ia um e-mail para o admin).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->string('garantia_padrao', 300)->nullable()->after('endereco');
            $table->string('prazo_entrega', 300)->nullable()->after('garantia_padrao');
            $table->text('entrega_info')->nullable()->after('prazo_entrega');   // entrega/retirada, bairros e taxas
            $table->text('trocas_info')->nullable()->after('entrega_info');
            $table->string('pagamento_info', 300)->nullable()->after('trocas_info');
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->string('garantia', 300)->nullable()->after('categoria');   // vazio = vale a garantia da loja
            $table->string('variacoes', 500)->nullable()->after('garantia');   // ex.: "Preto, Branco, Azul"
            $table->unsignedInteger('estoque')->nullable()->after('variacoes'); // vazio = não informado
        });

        Schema::create('produto_interesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->cascadeOnDelete();
            $table->foreignId('produto_id')->nullable()->constrained('produtos')->nullOnDelete();
            $table->string('nome', 120);
            $table->string('telefone', 30);
            $table->string('origem', 20)->default('botao'); // botao | assistente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produto_interesses');
        Schema::table('produtos', fn (Blueprint $t) => $t->dropColumn(['garantia', 'variacoes', 'estoque']));
        Schema::table('lojas', fn (Blueprint $t) => $t->dropColumn(['garantia_padrao', 'prazo_entrega', 'entrega_info', 'trocas_info', 'pagamento_info']));
    }
};
