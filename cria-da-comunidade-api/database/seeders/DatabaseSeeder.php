<?php

namespace Database\Seeders;

use App\Models\Comunidade;
use App\Models\Evento;
use App\Models\Profissional;
use App\Models\Projeto;
use App\Models\User;
use App\Models\Vaga;
use App\Models\VagaBeneficio;
use App\Models\VagaRequisito;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = ['super_admin', 'admin', 'moderador', 'usuario'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Super admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@criadacomunidade.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'handle'   => 'superadmin',
                'ativo'    => true,
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Comunidade
        $comunidade = Comunidade::create([
            'nome'      => 'Comunidade PPG',
            'cidade'    => 'São Paulo',
            'estado'    => 'SP',
            'descricao' => 'A maior comunidade de empreendedores da periferia.',
            'ativa'     => true,
        ]);

        // Usuário demo
        $usuario = User::updateOrCreate(
            ['email' => 'maria@exemplo.com'],
            [
                'name'          => 'Maria Silva',
                'password'      => Hash::make('password'),
                'handle'        => 'mariasilva',
                'whatsapp'      => '11999990000',
                'comunidade_id' => $comunidade->id,
                'ativo'         => true,
            ]
        );
        $usuario->assignRole('usuario');

        // Profissionais
        $profissionais = [
            [
                'nome'              => 'Ana Oliveira',
                'categoria'         => 'Beleza',
                'cargo'             => 'Manicure & Pedicure',
                'bio'               => 'Especialista em unhas decoradas há 8 anos.',
                'estrelas'          => 4.9,
                'total_avaliacoes'  => 312,
                'total_atendimentos'=> 1240,
                'preco_a_partir'    => 45.00,
                'whatsapp'          => '11911110000',
                'cor1'              => '#FF5E1A',
                'cor2'              => '#FFD23F',
                'verificado'        => true,
                'ativo'             => true,
            ],
            [
                'nome'              => 'Carlos Souza',
                'categoria'         => 'Tecnologia',
                'cargo'             => 'Dev Full Stack',
                'bio'               => 'Desenvolvedor com foco em produtos para a periferia.',
                'estrelas'          => 4.7,
                'total_avaliacoes'  => 89,
                'total_atendimentos'=> 156,
                'preco_a_partir'    => 120.00,
                'whatsapp'          => '11922220000',
                'cor1'              => '#2BD96B',
                'cor2'              => '#0D0B09',
                'verificado'        => true,
                'ativo'             => true,
            ],
            [
                'nome'              => 'Fernanda Lima',
                'categoria'         => 'Educação',
                'cargo'             => 'Professora Particular',
                'bio'               => 'Reforço escolar do fund. ao médio.',
                'estrelas'          => 4.8,
                'total_avaliacoes'  => 204,
                'total_atendimentos'=> 890,
                'preco_a_partir'    => 60.00,
                'cor1'              => '#FFD23F',
                'cor2'              => '#FF5E1A',
                'verificado'        => false,
                'ativo'             => true,
            ],
        ];

        foreach ($profissionais as $data) {
            Profissional::create([...$data, 'comunidade_id' => $comunidade->id]);
        }

        // Eventos
        $eventos = [
            [
                'titulo'         => 'Feira de Empreendedores PPG',
                'descricao'      => 'Encontro de empreendedores locais para networking e vendas.',
                'categoria'      => 'Negócios',
                'data_hora'      => now()->addDays(15),
                'local'          => 'Praça Central PPG, São Paulo - SP',
                'gratuito'       => true,
                'confirmados'    => 87,
                'interessados'   => 203,
                'cor1'           => '#FF5E1A',
                'cor2'           => '#FFD23F',
                'destaque'       => true,
                'ativo'          => true,
            ],
            [
                'titulo'         => 'Workshop: Como Vender pelo WhatsApp',
                'descricao'      => 'Aprenda técnicas de vendas usando o WhatsApp Business.',
                'categoria'      => 'Educação',
                'data_hora'      => now()->addDays(7),
                'local'          => 'Online (Zoom)',
                'gratuito'       => false,
                'preco'          => 29.90,
                'confirmados'    => 45,
                'interessados'   => 120,
                'cor1'           => '#2BD96B',
                'cor2'           => '#0D0B09',
                'destaque'       => false,
                'ativo'          => true,
            ],
        ];

        foreach ($eventos as $data) {
            Evento::create([...$data, 'organizador_id' => $superAdmin->id, 'comunidade_id' => $comunidade->id]);
        }

        // Projetos
        $projetos = [
            [
                'nome'          => 'Escola de Código',
                'descricao'     => 'Ensinamos programação gratuitamente para jovens da comunidade.',
                'icone'         => '💻',
                'causa'         => 'Educação',
                'cor'           => '#FF5E1A',
                'meta'          => 50000.00,
                'arrecadado'    => 32400.00,
                'progresso'     => 65,
                'impacto_valor' => '340',
                'impacto_label' => 'jovens impactados',
                'anos_atuando'  => 3,
                'cta_label'     => 'Apoiar projeto',
                'ativo'         => true,
            ],
            [
                'nome'          => 'Horta Comunitária',
                'descricao'     => 'Produção de alimentos orgânicos para famílias em vulnerabilidade.',
                'icone'         => '🌱',
                'causa'         => 'Alimentação',
                'cor'           => '#2BD96B',
                'meta'          => 15000.00,
                'arrecadado'    => 9800.00,
                'progresso'     => 65,
                'impacto_valor' => '120',
                'impacto_label' => 'famílias atendidas',
                'anos_atuando'  => 2,
                'cta_label'     => 'Apoiar projeto',
                'ativo'         => true,
            ],
        ];

        foreach ($projetos as $data) {
            Projeto::create([...$data, 'responsavel_id' => $superAdmin->id, 'comunidade_id' => $comunidade->id]);
        }

        // Vagas
        $vaga = Vaga::create([
            'anunciante_id'  => $superAdmin->id,
            'comunidade_id'  => $comunidade->id,
            'titulo'         => 'Designer Gráfico',
            'empresa'        => 'Agência Raiz',
            'descricao'      => 'Buscamos designer com experiência em identidade visual para marcas periféricas.',
            'local'          => 'São Paulo - SP (Híbrido)',
            'tipo'           => 'PJ',
            'salario'        => 'R$ 3.000',
            'salario_periodo'=> 'mês',
            'logo_cor'       => '#FF5E1A',
            'logo_iniciais'  => 'AR',
            'urgente'        => true,
            'ativa'          => true,
        ]);

        VagaRequisito::insert([
            ['vaga_id' => $vaga->id, 'descricao' => 'Domínio de Adobe Illustrator e Photoshop', 'nivel' => 'obrigatório', 'ordem' => 0],
            ['vaga_id' => $vaga->id, 'descricao' => 'Portfolio com projetos de branding', 'nivel' => 'obrigatório', 'ordem' => 1],
            ['vaga_id' => $vaga->id, 'descricao' => 'Experiência com motion design', 'nivel' => 'desejável', 'ordem' => 2],
        ]);

        VagaBeneficio::insert([
            ['vaga_id' => $vaga->id, 'descricao' => 'Trabalho remoto 3x por semana', 'ordem' => 0],
            ['vaga_id' => $vaga->id, 'descricao' => 'Vale alimentação R$ 600/mês', 'ordem' => 1],
            ['vaga_id' => $vaga->id, 'descricao' => 'Mentoria com profissionais sênior', 'ordem' => 2],
        ]);
    }
}
