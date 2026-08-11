<?php

namespace App\Observers;

use App\Mail\MatchCurriculosMail;
use App\Models\Curriculo;
use App\Models\Vaga;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VagaObserver
{
    public function created(Vaga $vaga): void
    {
        if (! $vaga->email_contato) {
            return;
        }

        // Extrai palavras relevantes do título da vaga (4+ chars)
        $palavras = collect(preg_split('/\s+/', mb_strtolower($vaga->titulo)))
            ->filter(fn($p) => mb_strlen($p) >= 4)
            ->unique()
            ->values()
            ->all();

        if (empty($palavras)) {
            return;
        }

        $query = Curriculo::where('publicado', true);

        if ($vaga->comunidade_id) {
            $query->where('comunidade_id', $vaga->comunidade_id);
        }

        $query->where(function ($q) use ($palavras) {
            foreach ($palavras as $palavra) {
                $q->orWhere('area_atuacao', 'like', "%{$palavra}%");
                // Busca dentro do JSON de habilidades
                $q->orWhereRaw('LOWER(JSON_UNQUOTE(habilidades)) LIKE ?', ["%{$palavra}%"]);
            }
        });

        $curriculos = $query->get();

        if ($curriculos->isEmpty()) {
            return;
        }

        try {
            Mail::to($vaga->email_contato)->send(new MatchCurriculosMail($vaga, $curriculos));
        } catch (\Throwable $e) {
            Log::error('Match currículos e-mail falhou', [
                'vaga_id' => $vaga->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
