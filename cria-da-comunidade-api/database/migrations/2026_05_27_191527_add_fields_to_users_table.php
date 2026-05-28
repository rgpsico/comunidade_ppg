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
        Schema::table('users', function (Blueprint $table) {
            $table->string('handle')->nullable()->unique()->after('name');
            $table->string('avatar')->nullable()->after('handle');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('whatsapp', 20)->nullable()->after('bio');
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete()->after('whatsapp');
            $table->boolean('ativo')->default(true)->after('comunidade_id');
            $table->timestamp('verified_at')->nullable()->after('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['handle', 'avatar', 'bio', 'whatsapp', 'comunidade_id', 'ativo', 'verified_at']);
        });
    }
};
