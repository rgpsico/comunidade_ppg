<?php

namespace Tests\Feature;

use App\Filament\Resources\CadastroProfissionalResource\Pages\ListCadastrosProfissionais;
use App\Models\CadastroProfissional;
use App\Models\Comunidade;
use App\Models\Profissional;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CadastroProfissionalAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole(Role::findOrCreate('super_admin', 'web'));

        return $u;
    }

    private function cadastro(array $over = []): CadastroProfissional
    {
        return CadastroProfissional::create(array_merge([
            'nome' => 'João da Silva', 'whatsapp' => '21999998888', 'categoria' => 'Beleza', 'profissao' => 'Barbeiro',
            'descricao' => 'Corte e barba', 'atende_domicilio' => true, 'tem_site' => false, 'tem_google_meu_negocio' => true,
            'nivel_digital' => 'basico', 'status' => 'novo',
        ], $over));
    }

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel('admin');
    }

    public function test_so_administradores_abrem_a_lista(): void
    {
        $this->cadastro();
        $this->get('/admin/cadastros-profissionais')->assertRedirect(); // visitante vai para o login

        $comum = User::factory()->create();
        $this->actingAs($comum)->get('/admin/cadastros-profissionais')->assertForbidden();

        $this->actingAs($this->admin())->get('/admin/cadastros-profissionais')->assertOk()->assertSee('João da Silva');
    }

    public function test_botao_do_link_existe_e_o_conteudo_traz_o_link_geral_um_por_comunidade_e_a_mensagem_de_boas_vindas(): void
    {
        Livewire::actingAs($this->admin());
        Livewire::test(ListCadastrosProfissionais::class)->assertActionExists('link');

        $html = view('filament.cadastro-link', [
            'base' => 'https://ppg.exemplo.test',
            'comunidades' => collect([
                (object) ['nome' => 'Complexo do Alemão', 'slug' => 'alemao'],
                (object) ['nome' => 'Sem slug', 'slug' => ''],
            ]),
        ])->render();

        $this->assertStringContainsString('https://ppg.exemplo.test/cadastro', $html);
        $this->assertStringContainsString('https://ppg.exemplo.test/alemao/cadastro', $html);
        $this->assertStringNotContainsString('Sem slug', $html); // comunidade sem slug não tem link próprio
        $this->assertStringContainsString('Enviar pelo WhatsApp', $html);
        $this->assertStringContainsString('https://wa.me/?text=', $html);
        $this->assertStringContainsString(rawurlencode('teve sorte'), $html); // mensagem pronta: "você teve sorte..."
        $this->assertStringContainsString('?ref=', $html);
    }

    public function test_aprovar_cria_o_perfil_no_portal_com_os_dados_do_cadastro(): void
    {
        $c = $this->cadastro();
        Livewire::actingAs($this->admin());

        Livewire::test(ListCadastrosProfissionais::class)->callTableAction('aprovar', $c);

        $c->refresh();
        $this->assertSame('aprovado', $c->status);
        $pro = Profissional::findOrFail($c->profissional_id);
        $this->assertSame('João da Silva', $pro->nome);
        $this->assertSame('Beleza', $pro->categoria);
        $this->assertSame('Barbeiro · Atende em domicílio', $pro->cargo);
        $this->assertSame('21999998888', $pro->whatsapp);
        $this->assertTrue($pro->ativo);
        $this->assertSame('Corte e barba', $pro->bio);
    }

    public function test_marcar_contatado_e_filtros(): void
    {
        $a = $this->cadastro();
        $b = $this->cadastro(['nome' => 'Maria', 'whatsapp' => '21988887777', 'tem_site' => true, 'nivel_digital' => 'avancado']);
        Livewire::actingAs($this->admin());

        Livewire::test(ListCadastrosProfissionais::class)
            ->callTableAction('contatado', $a)
            ->filterTable('nivel_digital', 'avancado')
            ->assertCanSeeTableRecords([$b])
            ->assertCanNotSeeTableRecords([$a]);

        $this->assertSame('contatado', $a->fresh()->status);
    }
}
