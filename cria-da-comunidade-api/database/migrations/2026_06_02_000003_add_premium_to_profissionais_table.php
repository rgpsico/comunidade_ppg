<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->string('plano', 20)->default('free')->after('ativo');         // free | premium
            $table->timestamp('premium_expira_em')->nullable()->after('plano');   // null = sem validade
        });
    }

    public function down(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->dropColumn(['plano', 'premium_expira_em']);
        });
    }
};
