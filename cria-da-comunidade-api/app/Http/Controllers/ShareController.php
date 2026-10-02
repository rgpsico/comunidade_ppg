<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Comunidade;
use App\Models\Evento;
use App\Models\Loja;
use App\Models\Pelada;
use App\Models\Produto;
use App\Models\Profissional;
use App\Models\Projeto;
use App\Models\Vaga;

class ShareController extends Controller
{
    private function comunidadeInfo(int|null $comunidadeId): array
    {
        if (!$comunidadeId) {
            return ['nome' => 'Cria da Comunidade', 'slug' => ''];
        }
        $c = Comunidade::find($comunidadeId);
        return [
            'nome' => $c?->nome ?? 'Cria da Comunidade',
            'slug' => $c?->slug ?? '',
        ];
    }

    private function frontUrl(string $slug, string $path): string
    {
        $base = config('app.frontend_url');
        return $slug ? "{$base}/{$slug}{$path}" : "{$base}{$path}";
    }

    public function profissional(Profissional $profissional): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($profissional->bio ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $image = ($profissional->foto
            ? $profissional->foto_url
            : null)
            ?? asset('images/og-default.png');

        $com = $this->comunidadeInfo($profissional->comunidade_id);

        return view('share.profissional', [
            'profissional' => $profissional,
            'ogTitle'      => "{$profissional->nome} — {$profissional->cargo}",
            'ogDesc'       => $desc ?: "{$profissional->categoria} · {$profissional->cargo}",
            'ogImage'      => $image,
            'siteName'     => $com['nome'],
            'redirectUrl'  => $this->frontUrl($com['slug'], "/profissionais/{$profissional->id}"),
        ]);
    }

    public function evento(Evento $evento): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($evento->descricao ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $dataFmt = $evento->data_hora
            ? \Carbon\Carbon::parse($evento->data_hora)->locale('pt_BR')->isoFormat('D [de] MMMM[,] HH[h]mm')
            : '';

        $image = $evento->imagem_capa_url
            ?? asset('images/og-default.png');

        $com = $this->comunidadeInfo($evento->comunidade_id ?? null);

        return view('share.evento', [
            'evento'      => $evento,
            'ogTitle'     => $evento->titulo,
            'ogDesc'      => $desc ?: "{$evento->categoria} · {$evento->local} · {$dataFmt}",
            'ogImage'     => $image,
            'siteName'    => $com['nome'],
            'redirectUrl' => $this->frontUrl($com['slug'], "/eventos/{$evento->id}"),
        ]);
    }

    public function pelada(Pelada $pelada): \Illuminate\Contracts\View\View
    {
        $tipos = ['quadra' => 'Quadra', 'society' => 'Society', 'campo' => 'Campo', 'racha' => 'Racha'];
        $formato = $pelada->goleiros_por_time > 0
            ? "{$pelada->jogadores_linha} na linha + gol"
            : "{$pelada->jogadores_linha} na linha";
        $naLista = $pelada->jogadores()->count();

        $partes = array_filter([
            $tipos[$pelada->tipo] ?? 'Pelada',
            $formato,
            "{$pelada->tempo_jogo_min} min por jogo",
            $pelada->quando,
            $pelada->local,
        ]);
        $desc = implode(' · ', $partes) . '. '
            . ($naLista > 0 ? "{$naLista} na lista" : 'Lista aberta')
            . ' — entre na fila pelo celular!';

        $com = $this->comunidadeInfo($pelada->comunidade_id);

        return view('share.pelada', [
            'pelada'      => $pelada,
            'ogTitle'     => "⚽ {$pelada->nome}",
            'ogDesc'      => $desc,
            'ogImage'     => asset('images/og-pelada.png'),
            'ogUrl'       => route('share.pelada', $pelada),
            'siteName'    => $com['nome'],
            'redirectUrl' => $this->frontUrl($com['slug'], "/futebol/{$pelada->id}"),
        ]);
    }

    public function loja(Loja $loja): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($loja->descricao ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $image = $loja->logo_url
            ?? asset('images/og-default.png');

        $com = $this->comunidadeInfo($loja->comunidade_id ?? null);

        return view('share.loja', [
            'loja'        => $loja,
            'ogTitle'     => "{$loja->nome} — {$loja->categoria}",
            'ogDesc'      => $desc ?: "{$loja->categoria} · {$loja->endereco}",
            'ogImage'     => $image,
            'siteName'    => $com['nome'],
            'redirectUrl' => $this->frontUrl($com['slug'], "/lojas/{$loja->id}"),
        ]);
    }

    public function projeto(Projeto $projeto): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($projeto->descricao ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $image = $projeto->imagem_capa_url ?? asset('images/og-default.png');

        $com = $this->comunidadeInfo($projeto->comunidade_id ?? null);

        return view('share.projeto', [
            'projeto'     => $projeto,
            'ogTitle'     => "{$projeto->nome} — Projeto Social",
            'ogDesc'      => $desc ?: "Projeto social da comunidade {$com['nome']}",
            'ogImage'     => $image,
            'siteName'    => $com['nome'],
            'redirectUrl' => $this->frontUrl($com['slug'], "/projetos/{$projeto->id}"),
        ]);
    }

    public function artigo(Artigo $artigo): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($artigo->resumo ?? $artigo->corpo ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $com = $this->comunidadeInfo($artigo->comunidade_id);

        return view('share.artigo', [
            'artigo'      => $artigo,
            'ogTitle'     => $artigo->titulo,
            'ogDesc'      => $desc ?: "{$artigo->categoria} · {$artigo->autor}",
            'ogImage'     => $artigo->imagem_capa_url ?? asset('images/og-default.png'),
            'siteName'    => $com['nome'],
            'redirectUrl' => $this->frontUrl($com['slug'], "/artigos/{$artigo->slug}"),
        ]);
    }

    public function vaga(Vaga $vaga): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($vaga->descricao ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $image = $vaga->logo_imagem_url
            ?? asset('images/og-default.png');

        $com = $this->comunidadeInfo($vaga->comunidade_id ?? null);

        return view('share.vaga', [
            'vaga'        => $vaga,
            'ogTitle'     => "{$vaga->titulo} — {$vaga->empresa}",
            'ogDesc'      => $desc ?: "{$vaga->tipo} · {$vaga->local} · {$vaga->salario}",
            'ogImage'     => $image,
            'siteName'    => $com['nome'],
            'redirectUrl' => $this->frontUrl($com['slug'], "/vagas/{$vaga->id}"),
        ]);
    }

    public function produto(Produto $produto): \Illuminate\Contracts\View\View
    {
        $produto->load('loja');
        $loja = $produto->loja;

        $desc = strip_tags($produto->descricao ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $preco = $produto->preco_promocional ?? $produto->preco;
        $precoStr = 'R$ ' . number_format($preco, 2, ',', '.');
        $lojaInfo = $loja ? " · {$loja->nome}" : '';

        $image = $produto->imagem_principal_url
            ?? ($loja?->logo_url)
            ?? asset('images/og-default.png');

        $com = $this->comunidadeInfo($loja?->comunidade_id ?? null);

        return view('share.produto', [
            'produto'     => $produto,
            'loja'        => $loja,
            'ogTitle'     => "{$produto->nome}{$lojaInfo}",
            'ogDesc'      => $desc ?: "{$precoStr}{$lojaInfo}",
            'ogImage'     => $image,
            'siteName'    => $com['nome'],
            'redirectUrl' => $this->frontUrl($com['slug'], "/lojas/{$loja?->id}/produtos/{$produto->id}"),
        ]);
    }
}
