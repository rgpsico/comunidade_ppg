<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            // Mototáxi é um profissional (categoria Transporte) com horário e status online.
            $table->boolean('mototaxi')->default(false)->index()->after('plano');
            $table->string('horario', 80)->nullable()->after('mototaxi');
            // Última vez que o painel do mototaxista avisou "estou rodando". Online = aviso recente.
            $table->timestamp('online_em')->nullable()->after('horario');
            $table->decimal('lat', 10, 7)->nullable()->after('online_em');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            // Chave do link privado do painel do mototaxista (sem senha, o link é a chave).
            $table->string('painel_token', 64)->nullable()->unique()->after('lng');
        });
    }

    public function down(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->dropUnique(['painel_token']);
            $table->dropIndex(['mototaxi']);
            $table->dropColumn(['mototaxi', 'horario', 'online_em', 'lat', 'lng', 'painel_token']);
        });
    }
};
