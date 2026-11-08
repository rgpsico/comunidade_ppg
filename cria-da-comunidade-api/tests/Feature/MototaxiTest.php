<?php

namespace Tests\Feature;

use App\Models\Comunidade;
use App\Models\Profissional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MototaxiTest extends TestCase
{
    use RefreshDatabase;

    private function cadastrar(array $over = []): string
    {
        return $this->postJson('/api/mototaxi/cadastro', array_merge(['nome' => 'Zé Moto', 'whatsapp' => '(21) 98888-7777'], $over))
            ->assertCreated()->json('data.token');
    }

    private function aprovar(string $token): Profissional
    {
        $p = Profissional::where('painel_token', $token)->firstOrFail();
        $p->update(['ativo' => true]);

        return $p;
    }

    public function test_cadastro_com_so_nome_e_telefone_entra_em_analise_e_devolve_o_link_do_painel(): void
    {
        Comunidade::create(['nome' => 'Cantagalo', 'slug' => 'cantagalo']);

        $token = $this->cadastrar(['horario' => 'Seg a sex, 6h às 14h']);

        $p = Profissional::where('painel_token', $token)->first();
        $this->assertSame('5521988887777', $p->whatsapp);
        $this->assertTrue($p->mototaxi);
        $this->assertFalse($p->ativo);
        $this->assertSame('Transporte', $p->categoria);
        $this->assertSame('Mototáxi', $p->cargo);
        $this->assertSame('Seg a sex, 6h às 14h', $p->horario);
        $this->assertSame(1, $p->comunidade_id);
        $this->assertSame(40, strlen($token));
    }

    public function test_horario_e_opcional_e_telefone_invalido_e_recusado(): void
    {
        $this->cadastrar();
        $this->postJson('/api/mototaxi/cadastro', ['nome' => 'Zé', 'whatsapp' => '123'])->assertStatus(422);
        $this->postJson('/api/mototaxi/cadastro', ['whatsapp' => '21988887777'])->assertStatus(422);
    }

    public function test_telefone_repetido_nao_entrega_o_token_de_outra_pessoa(): void
    {
        $this->cadastrar();

        $r = $this->postJson('/api/mototaxi/cadastro', ['nome' => 'Intruso', 'whatsapp' => '21 98888-7777'])->assertOk();
        $this->assertTrue($r->json('data.ja_cadastrado'));
        $this->assertNull($r->json('data.token'));
        $this->assertSame(1, Profissional::count());
    }

    public function test_isca_anti_robo_nao_grava(): void
    {
        $this->postJson('/api/mototaxi/cadastro', ['nome' => 'Robo', 'whatsapp' => '21988887777', 'apelido' => 'x'])->assertCreated();
        $this->assertSame(0, Profissional::count());
    }

    public function test_so_aprovados_aparecem_na_lista_e_sem_token_nem_posicao(): void
    {
        $pendente = $this->cadastrar(['nome' => 'Pendente', 'whatsapp' => '21911112222']);
        $aprovado = $this->aprovar($this->cadastrar(['nome' => 'Aprovado', 'whatsapp' => '21933334444']));

        $r = $this->getJson('/api/mototaxis')->assertOk();
        $this->assertSame(['Aprovado'], collect($r->json('data'))->pluck('nome')->all());
        $this->assertArrayNotHasKey('painel_token', $r->json('data.0'));
        $this->assertArrayNotHasKey('lat', $r->json('data.0'));
        $this->assertNotNull($pendente);
        $this->assertNotNull($aprovado);
    }

    public function test_token_e_posicao_nao_vazam_pela_api_de_profissionais(): void
    {
        $p = $this->aprovar($this->cadastrar());
        $p->forceFill(['lat' => -22.9, 'lng' => -43.2])->save();

        $r = $this->getJson("/api/profissionais/{$p->id}")->assertOk();
        $this->assertArrayNotHasKey('painel_token', $r->json());
        $this->assertArrayNotHasKey('lat', $r->json());
        $this->assertArrayNotHasKey('lng', $r->json());

        $lista = $this->getJson('/api/profissionais')->assertOk();
        $this->assertStringNotContainsString('painel_token', $lista->getContent());
    }

    public function test_ping_deixa_online_e_sem_ping_volta_a_offline(): void
    {
        $token = $this->cadastrar();
        $this->aprovar($token);

        $this->assertFalse($this->getJson('/api/mototaxis')->json('data.0.online'));

        $this->postJson("/api/mototaxi/painel/$token/ping", ['lat' => -22.98, 'lng' => -43.21])
            ->assertOk()->assertJsonPath('data.online', true);

        $r = $this->getJson('/api/mototaxis');
        $this->assertTrue($r->json('data.0.online'));
        $this->assertSame(1, $r->json('online'));
        $this->assertEquals(-22.98, Profissional::where('painel_token', $token)->value('lat'));

        $this->travel(Profissional::ONLINE_SEGUNDOS + 5)->seconds();
        $this->assertFalse($this->getJson('/api/mototaxis')->json('data.0.online'));
        $this->assertSame(0, $this->getJson('/api/mototaxis')->json('online'));
    }

    public function test_ping_sem_localizacao_tambem_vale(): void
    {
        $token = $this->cadastrar();
        $this->postJson("/api/mototaxi/painel/$token/ping", [])->assertOk()->assertJsonPath('data.online', true);
    }

    public function test_ficar_offline_manualmente(): void
    {
        $token = $this->cadastrar();
        $this->postJson("/api/mototaxi/painel/$token/ping", [])->assertOk();
        $this->postJson("/api/mototaxi/painel/$token/offline")->assertOk()->assertJsonPath('data.online', false);
    }

    public function test_online_primeiro_na_lista(): void
    {
        $a = $this->cadastrar(['nome' => 'Ana', 'whatsapp' => '21911110001']);
        $b = $this->cadastrar(['nome' => 'Bia', 'whatsapp' => '21911110002']);
        $this->aprovar($a);
        $this->aprovar($b);
        $this->postJson("/api/mototaxi/painel/$b/ping", [])->assertOk();

        $this->assertSame(['Bia', 'Ana'], collect($this->getJson('/api/mototaxis')->json('data'))->pluck('nome')->all());
    }

    public function test_token_errado_e_coordenada_absurda_sao_recusados(): void
    {
        $token = $this->cadastrar();

        $this->getJson('/api/mototaxi/painel/' . str_repeat('x', 40))->assertNotFound();
        $this->postJson('/api/mototaxi/painel/curto/ping', [])->assertStatus(422);
        $this->postJson("/api/mototaxi/painel/$token/ping", ['lat' => 500, 'lng' => 0])->assertStatus(422);
    }

    public function test_profissional_comum_nao_abre_painel_de_mototaxi(): void
    {
        $p = Profissional::create(['nome' => 'Barbeiro', 'categoria' => 'Beleza', 'cargo' => 'Barbeiro', 'painel_token' => str_repeat('a', 40)]);
        $this->getJson('/api/mototaxi/painel/' . $p->painel_token)->assertNotFound();
    }
}
