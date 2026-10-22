<?php

namespace Tests\Feature;

use App\Models\CadastroProfissional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CadastroProfissionalTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $over = []): array
    {
        return array_merge([
            'nome' => 'João da Silva', 'whatsapp' => '(21) 99999-8888', 'categoria' => 'Construção', 'profissao' => 'Eletricista',
            'tem_site' => false, 'tem_google_meu_negocio' => false, 'nivel_digital' => 'basico',
        ], $over);
    }

    public function test_cadastro_minimo_funciona_sem_login_e_normaliza_o_whatsapp(): void
    {
        $this->postJson('/api/cadastro-profissional', $this->payload())
            ->assertCreated()->assertJsonPath('data.nome', 'João');

        $c = CadastroProfissional::first();
        $this->assertSame('21999998888', $c->whatsapp);
        $this->assertSame('novo', $c->status);
        $this->assertNull($c->email);
        $this->assertNull($c->atende_domicilio); // não se aplica
        $this->assertSame('https://wa.me/5521999998888', $c->whatsapp_url);
    }

    public function test_cadastro_completo_guarda_todos_os_campos(): void
    {
        $this->postJson('/api/cadastro-profissional', $this->payload([
            'nome' => 'Maria Barbeira', 'whatsapp' => '+55 21 98888-7777', 'email' => 'MARIA@Exemplo.com', 'categoria' => 'Beleza', 'profissao' => 'Barbeiro',
            'descricao' => 'Corte <b>masculino</b> e barba.', 'atende_domicilio' => true, 'tem_site' => true, 'tem_google_meu_negocio' => true,
            'nivel_digital' => 'avancado', 'origem' => 'ana<script>',
        ]))->assertCreated();

        $c = CadastroProfissional::first();
        $this->assertSame('21988887777', $c->whatsapp);
        $this->assertSame('maria@exemplo.com', $c->email);
        $this->assertTrue($c->atende_domicilio);
        $this->assertTrue($c->tem_site);
        $this->assertTrue($c->tem_google_meu_negocio);
        $this->assertSame('avancado', $c->nivel_digital);
        $this->assertSame('Corte masculino e barba.', $c->descricao); // sem HTML
        $this->assertSame('ana', $c->origem); // tags HTML somem
    }

    public function test_validacoes(): void
    {
        $this->postJson('/api/cadastro-profissional', [])->assertStatus(422)->assertJsonValidationErrors(['nome', 'whatsapp', 'categoria', 'profissao', 'nivel_digital']);
        $this->postJson('/api/cadastro-profissional', $this->payload(['whatsapp' => '12345']))->assertStatus(422)->assertJsonValidationErrors('whatsapp');
        $this->postJson('/api/cadastro-profissional', $this->payload(['categoria' => 'Invencao']))->assertStatus(422)->assertJsonValidationErrors('categoria');
        $this->postJson('/api/cadastro-profissional', $this->payload(['nivel_digital' => 'expert']))->assertStatus(422)->assertJsonValidationErrors('nivel_digital');
        $this->postJson('/api/cadastro-profissional', $this->payload(['email' => 'nao-e-email']))->assertStatus(422)->assertJsonValidationErrors('email');
        $this->assertSame(0, CadastroProfissional::count());
    }

    public function test_reenvio_atualiza_sem_duplicar_e_nao_altera_quem_ja_foi_aprovado(): void
    {
        $this->postJson('/api/cadastro-profissional', $this->payload())->assertCreated();
        $this->postJson('/api/cadastro-profissional', $this->payload(['nome' => 'João Corrigido', 'whatsapp' => '21 99999 8888']))->assertCreated();
        $this->assertSame(1, CadastroProfissional::count());
        $this->assertSame('João Corrigido', CadastroProfissional::first()->nome);

        CadastroProfissional::first()->update(['status' => 'aprovado']);
        $this->postJson('/api/cadastro-profissional', $this->payload(['nome' => 'Outra Pessoa']))->assertCreated(); // mesma resposta: não revela nada
        $this->assertSame('João Corrigido', CadastroProfissional::first()->nome);
        $this->assertSame('aprovado', CadastroProfissional::first()->status);
    }

    public function test_isca_anti_robo_nao_grava_nada(): void
    {
        $this->postJson('/api/cadastro-profissional', $this->payload(['apelido' => 'comprar seguidores']))->assertCreated();
        $this->assertSame(0, CadastroProfissional::count());
    }

    public function test_limite_de_envios_por_ip(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->postJson('/api/cadastro-profissional', $this->payload(['whatsapp' => '2199999' . str_pad((string) $i, 4, '0', STR_PAD_LEFT)]))->assertCreated();
        }
        $this->postJson('/api/cadastro-profissional', $this->payload(['whatsapp' => '21999990099']))->assertStatus(429);
    }

    public function test_lead_e_salvo_no_passo_1_e_vira_parcial_com_utm(): void
    {
        $this->postJson('/api/cadastro-profissional/progresso', [
            'nome' => 'Carlos Eletricista', 'whatsapp' => '(21) 98888-1111', 'etapa' => 1,
            'origem' => 'fb-eletricista', 'utm_source' => 'fb', 'utm_campaign' => 'Teste Zona Sul', 'utm_content' => 'Anuncio 1 | Eletricista',
        ])->assertCreated();

        $c = CadastroProfissional::first();
        $this->assertSame('parcial', $c->status);
        $this->assertSame(1, $c->etapa);
        $this->assertSame('21988881111', $c->whatsapp);
        $this->assertNull($c->categoria);
        $this->assertSame('fb-eletricista', $c->origem);
        $this->assertSame('Anuncio 1 | Eletricista', $c->utm_content);
    }

    public function test_passo_2_avanca_sem_voltar_e_o_envio_final_completa_o_mesmo_lead(): void
    {
        $base = ['nome' => 'Carlos', 'whatsapp' => '21988881111', 'origem' => 'fb-eletricista'];
        $this->postJson('/api/cadastro-profissional/progresso', $base + ['etapa' => 1])->assertCreated();
        $this->postJson('/api/cadastro-profissional/progresso', $base + ['etapa' => 2, 'categoria' => 'Construção', 'profissao' => 'Eletricista'])->assertCreated();
        $this->postJson('/api/cadastro-profissional/progresso', $base + ['etapa' => 1])->assertCreated(); // voltou e reenviou

        $c = CadastroProfissional::first();
        $this->assertSame(2, $c->etapa);
        $this->assertSame('Eletricista', $c->profissao);
        $this->assertSame(1, CadastroProfissional::count());

        $this->postJson('/api/cadastro-profissional', $this->payload(['nome' => 'Carlos', 'whatsapp' => '21988881111'])) // sem origem no reenvio final
            ->assertCreated();

        $c->refresh();
        $this->assertSame(1, CadastroProfissional::count());
        $this->assertSame('novo', $c->status);
        $this->assertSame(3, $c->etapa);
        $this->assertSame('fb-eletricista', $c->origem); // a origem do anúncio não se perde
    }

    public function test_progresso_nao_mexe_em_quem_ja_completou_e_valida_whatsapp_e_isca(): void
    {
        $this->postJson('/api/cadastro-profissional', $this->payload())->assertCreated();
        $this->postJson('/api/cadastro-profissional/progresso', ['nome' => 'Outro Nome', 'whatsapp' => '21999998888', 'etapa' => 1])->assertCreated();
        $this->assertSame('João da Silva', CadastroProfissional::first()->nome);
        $this->assertSame('novo', CadastroProfissional::first()->status);

        $this->postJson('/api/cadastro-profissional/progresso', ['nome' => 'X Y', 'whatsapp' => '123', 'etapa' => 1])->assertStatus(422)->assertJsonValidationErrors('whatsapp');
        $this->postJson('/api/cadastro-profissional/progresso', ['nome' => 'Robo Bot', 'whatsapp' => '21911112222', 'etapa' => 1, 'apelido' => 'spam'])->assertCreated();
        $this->assertSame(1, CadastroProfissional::count());
    }

    public function test_normalizacao_de_whatsapp(): void
    {
        foreach (['(21) 99999-8888' => '21999998888', '021999998888' => '21999998888', '5521999998888' => '21999998888', '2133334444' => '2133334444'] as $in => $out) {
            $this->assertSame($out, CadastroProfissional::normalizarWhatsapp((string) $in), $in);
        }
        foreach (['', '123', '99999-8888', '00999998888', 'abc'] as $bad) {
            $this->assertNull(CadastroProfissional::normalizarWhatsapp($bad), $bad);
        }
    }
}
