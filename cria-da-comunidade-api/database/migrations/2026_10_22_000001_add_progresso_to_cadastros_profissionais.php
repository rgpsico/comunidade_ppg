<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadastros_profissionais', function (Blueprint $table) {
            // O lead é salvo já no passo 1 (nome + WhatsApp): quem abandona continua contatável.
            $table->string('categoria', 40)->nullable()->change();
            $table->string('profissao', 80)->nullable()->change();
            $table->string('nivel_digital', 12)->nullable()->change();

            $table->unsignedTinyInteger('etapa')->default(1)->after('status');   // último passo concluído (1 a 3)
            $table->string('utm_source', 40)->nullable()->after('origem');
            $table->string('utm_campaign', 120)->nullable()->after('utm_source');
            $table->string('utm_content', 120)->nullable()->after('utm_campaign');
        });

        // Os cadastros que já existiam estão completos.
        DB::table('cadastros_profissionais')->update(['etapa' => 3]);
    }

    public function down(): void
    {
        Schema::table('cadastros_profissionais', function (Blueprint $table) {
            $table->dropColumn(['etapa', 'utm_source', 'utm_campaign', 'utm_content']);
        });
    }
};
