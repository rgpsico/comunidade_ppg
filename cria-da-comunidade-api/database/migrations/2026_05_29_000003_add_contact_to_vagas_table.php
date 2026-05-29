<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vagas', function (Blueprint $table) {
            $table->string('whatsapp', 20)->nullable()->after('logo_imagem');
            $table->string('email_contato', 150)->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('vagas', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'email_contato']);
        });
    }
};
