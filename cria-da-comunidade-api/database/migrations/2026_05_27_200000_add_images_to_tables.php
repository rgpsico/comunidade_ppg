<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('bio');
            $table->json('galeria')->nullable()->after('foto');
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->string('imagem_capa')->nullable()->after('descricao');
            $table->json('galeria')->nullable()->after('imagem_capa');
        });

        Schema::table('projetos', function (Blueprint $table) {
            $table->string('imagem_capa')->nullable()->after('descricao');
            $table->json('galeria')->nullable()->after('imagem_capa');
        });

        Schema::table('vagas', function (Blueprint $table) {
            $table->string('logo_imagem')->nullable()->after('logo_iniciais');
        });
    }

    public function down(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->dropColumn(['foto', 'galeria']);
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['imagem_capa', 'galeria']);
        });

        Schema::table('projetos', function (Blueprint $table) {
            $table->dropColumn(['imagem_capa', 'galeria']);
        });

        Schema::table('vagas', function (Blueprint $table) {
            $table->dropColumn('logo_imagem');
        });
    }
};
