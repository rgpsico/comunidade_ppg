<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CadastroProfissional extends Model
{
    protected $table = 'cadastros_profissionais';

    public const CATEGORIAS = [
        'Alimentação', 'Beleza', 'Casa', 'Construção', 'Educação', 'Eventos',
        'Pet', 'Saúde', 'Serviços', 'Tecnologia', 'Transporte',
    ];

    public const NIVEIS = ['basico' => 'Só o básico', 'avancado' => 'Avançado no digital'];

    public const STATUS = ['parcial' => 'Não terminou', 'novo' => 'Novo', 'contatado' => 'Contatado', 'aprovado' => 'Aprovado', 'descartado' => 'Descartado'];

    protected $fillable = [
        'comunidade_id', 'nome', 'whatsapp', 'email', 'categoria', 'profissao', 'descricao',
        'atende_domicilio', 'tem_site', 'tem_google_meu_negocio', 'nivel_digital',
        'status', 'etapa', 'profissional_id', 'origem', 'utm_source', 'utm_campaign', 'utm_content', 'observacao_admin',
    ];

    protected function casts(): array
    {
        return [
            'atende_domicilio'       => 'boolean',
            'tem_site'               => 'boolean',
            'tem_google_meu_negocio' => 'boolean',
        ];
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function profissional(): BelongsTo
    {
        return $this->belongsTo(Profissional::class);
    }

    /** DDD + número, só dígitos. Aceita "(21) 99999-8888", "+55 21 99999-8888", "021999998888". Null se inválido. */
    public static function normalizarWhatsapp(?string $valor): ?string
    {
        $d = preg_replace('/\D+/', '', (string) $valor) ?? '';
        if (strlen($d) > 11 && str_starts_with($d, '55')) {
            $d = substr($d, 2);
        }
        $d = ltrim($d, '0');

        // 10 dígitos (fixo) ou 11 (celular com 9); DDD de 11 a 99
        return preg_match('/^[1-9][1-9]\d{8,9}$/', $d) ? $d : null;
    }

    public function getWhatsappUrlAttribute(): string
    {
        return 'https://wa.me/55' . $this->whatsapp;
    }
}
