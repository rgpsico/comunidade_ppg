<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class QuadraHorario extends Model
{
    public const DIAS = [
        0 => 'Domingo',
        1 => 'Segunda',
        2 => 'Terça',
        3 => 'Quarta',
        4 => 'Quinta',
        5 => 'Sexta',
        6 => 'Sábado',
    ];

    protected $fillable = ['quadra_time_id', 'dia_semana', 'hora_inicio', 'hora_fim'];

    protected $casts = [
        'dia_semana' => 'integer',
    ];

    protected static function booted(): void
    {
        // Rede de segurança: nenhum caminho (admin, tinker, API futura) grava horário
        // sobreposto com o de OUTRO time da mesma quadra.
        static::saving(function (QuadraHorario $h) {
            $time = QuadraTime::find($h->quadra_time_id);
            if (! $time || ! $time->ativo) {
                return;
            }

            $conflito = static::conflito(
                $time->quadra_id,
                (int) $h->dia_semana,
                (string) $h->hora_inicio,
                (string) $h->hora_fim,
                $time->id,
            );

            if ($conflito) {
                throw ValidationException::withMessages([
                    'horarios' => static::mensagemConflito($conflito),
                ]);
            }
        });
    }

    public function time(): BelongsTo
    {
        return $this->belongsTo(QuadraTime::class, 'quadra_time_id');
    }

    /** "HH:MM" a partir de "HH:MM" ou "HH:MM:SS". */
    public static function hm(string $hora): string
    {
        return substr($hora, 0, 5);
    }

    public function rotulo(): string
    {
        return substr(self::DIAS[$this->dia_semana], 0, 3) . ' '
            . self::hm($this->hora_inicio) . '–' . self::hm($this->hora_fim);
    }

    /**
     * Primeiro horário de OUTRO time ativo da mesma quadra que se sobrepõe ao intervalo.
     * Intervalos que apenas se encostam (fim == início) não conflitam.
     */
    public static function conflito(int $quadraId, int $dia, string $inicio, string $fim, ?int $ignorarTimeId = null): ?self
    {
        $inicio = self::hm($inicio) . ':00';
        $fim = self::hm($fim) . ':00';

        return static::query()
            ->with('time:id,nome,quadra_id')
            ->where('dia_semana', $dia)
            ->where('hora_inicio', '<', $fim)
            ->where('hora_fim', '>', $inicio)
            ->whereHas('time', function ($q) use ($quadraId, $ignorarTimeId) {
                $q->where('quadra_id', $quadraId)->where('ativo', true);
                if ($ignorarTimeId) {
                    $q->where('id', '!=', $ignorarTimeId);
                }
            })
            ->first();
    }

    public static function mensagemConflito(self $conflito): string
    {
        return sprintf(
            '%s %s–%s já pertence a "%s" nesta quadra.',
            self::DIAS[$conflito->dia_semana],
            self::hm($conflito->hora_inicio),
            self::hm($conflito->hora_fim),
            $conflito->time?->nome ?? 'outro time',
        );
    }
}
