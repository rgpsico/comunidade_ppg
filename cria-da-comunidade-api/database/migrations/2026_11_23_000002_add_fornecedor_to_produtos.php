<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Revenda: de quem o lojista compra o produto quando vende. Dado interno: nunca vai para o cliente
 * nem para o assistente; aparece só para o dono/admin e no aviso de venda.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->string('fornecedor_nome', 150)->nullable()->after('estoque');
            $table->string('fornecedor_contato', 60)->nullable()->after('fornecedor_nome'); // WhatsApp/telefone
            $table->string('fornecedor_obs', 500)->nullable()->after('fornecedor_contato'); // endereço, preço combinado...
        });
    }

    public function down(): void
    {
        Schema::table('produtos', fn (Blueprint $t) => $t->dropColumn(['fornecedor_nome', 'fornecedor_contato', 'fornecedor_obs']));
    }
};
