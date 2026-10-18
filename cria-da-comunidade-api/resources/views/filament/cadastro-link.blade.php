@php
    $mensagem = fn (string $link) => "Olá! 👋 Boas-vindas à Comunidade PPG!\n\nVocê teve sorte: o dono do sistema viu que você é um(a) ótimo(a) profissional e quer você aqui com a gente. 🌟\n\nFaça o seu cadastro rápido (leva 2 minutos):\n{$link}";
    $itens = collect([['nome' => 'Link geral', 'link' => $base . '/cadastro']])
        ->merge($comunidades->filter(fn ($c) => $c->slug)->map(fn ($c) => ['nome' => $c->nome, 'link' => $base . '/' . $c->slug . '/cadastro']));
@endphp

<div class="space-y-5 text-sm" x-data="{ copiado: null, copiar(id, texto) { navigator.clipboard.writeText(texto).then(() => { this.copiado = id; setTimeout(() => this.copiado = null, 2000) }) } }">
    <p>Mande o link no WhatsApp para os profissionais da região. Eles preenchem em cerca de 2 minutos, no celular, e o cadastro cai aqui para você chamar no WhatsApp.</p>

    @foreach ($itens as $i => $item)
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4 space-y-3">
            <div class="font-semibold">{{ $item['nome'] }}</div>
            <div class="flex gap-2 items-center">
                <input type="text" readonly value="{{ $item['link'] }}" class="fi-input w-full rounded-lg border border-gray-300 dark:border-white/10 bg-transparent px-3 py-2 font-mono text-xs" onclick="this.select()">
                <button type="button" class="rounded-lg bg-primary-600 px-3 py-2 text-white font-medium whitespace-nowrap" x-on:click="copiar('l{{ $i }}', @js($item['link']))">
                    <span x-show="copiado !== 'l{{ $i }}'">Copiar link</span><span x-show="copiado === 'l{{ $i }}'" x-cloak>Copiado ✓</span>
                </button>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="https://wa.me/?text={{ rawurlencode($mensagem($item['link'])) }}" target="_blank" rel="noopener" class="rounded-lg bg-green-600 px-3 py-2 text-white font-medium">Enviar pelo WhatsApp</a>
                <button type="button" class="rounded-lg border border-gray-300 dark:border-white/20 px-3 py-2 font-medium" x-on:click="copiar('m{{ $i }}', @js($mensagem($item['link'])))">
                    <span x-show="copiado !== 'm{{ $i }}'">Copiar mensagem pronta</span><span x-show="copiado === 'm{{ $i }}'" x-cloak>Copiada ✓</span>
                </button>
            </div>
        </div>
    @endforeach

    <p class="text-xs text-gray-500">Dica: para saber quem trouxe cada cadastro, acrescente <code>?ref=nome</code> ao final do link (ex.: <code>.../cadastro?ref=maria</code>). Aparece na coluna "Origem".</p>
</div>
