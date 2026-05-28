<?php

namespace Database\Seeders;

use App\Models\Comunidade;
use App\Models\Profissional;
use Illuminate\Database\Seeder;

class ProfissionaisComunitariosSeeder extends Seeder
{
    public function run(): void
    {
        $comunidade = Comunidade::first();

        $profissionais = [
            [
                'nome'       => 'DL FARMA Pistão',
                'categoria'  => 'Saúde',
                'cargo'      => 'Farmácia e Perfumaria',
                'bio'        => 'Perfumaria e farmácia DL FARMA no Pistão.',
                'whatsapp'   => '5521974261093',
                'cor1'       => '#2BD96B',
                'cor2'       => '#0D0B09',
                'verificado' => false,
            ],
            [
                'nome'       => 'El Santos Burguer',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Hamburgueria e Pizzaria Artesanal',
                'bio'        => 'Hamburgueria e Pizzaria Artesanal.',
                'whatsapp'   => '5521982864673',
                'cor1'       => '#FF5E1A',
                'cor2'       => '#FFD23F',
                'verificado' => false,
            ],
            [
                'nome'       => 'André Geladeira',
                'categoria'  => 'Casa',
                'cargo'      => 'Técnico de Geladeira',
                'bio'        => 'Conserto e manutenção de geladeiras e eletrodomésticos.',
                'whatsapp'   => '5521974175073',
                'cor1'       => '#3B82F6',
                'cor2'       => '#1E40AF',
                'verificado' => false,
            ],
            [
                'nome'       => 'Tati Rações',
                'categoria'  => 'Pet',
                'cargo'      => 'Rações e Pet Shop',
                'bio'        => 'Venda de rações e produtos para animais.',
                'whatsapp'   => '5521964948555',
                'cor1'       => '#F59E0B',
                'cor2'       => '#92400E',
                'verificado' => false,
            ],
            [
                'nome'       => 'Artesanal e Esfiha Mineira',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Esfiha e Comida Artesanal',
                'bio'        => 'Esfihas e comida artesanal mineira. Também vende o melhor açaí!',
                'whatsapp'   => '5521998206635',
                'cor1'       => '#FF5E1A',
                'cor2'       => '#FFD23F',
                'verificado' => false,
            ],
            [
                'nome'       => 'Talento do Trig',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Pizzaria Artesanal',
                'bio'        => 'Anastácia pizzaria — Pizzas artesanais feitas com ingredientes selecionados.',
                'whatsapp'   => '5521981861097',
                'cor1'       => '#EF4444',
                'cor2'       => '#7F1D1D',
                'verificado' => false,
            ],
            [
                'nome'       => 'Davi Lanches Cantagalo',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Lanches e Delivery',
                'bio'        => 'Lanches e delivery no Cantagalo.',
                'whatsapp'   => '5521972938347',
                'cor1'       => '#F59E0B',
                'cor2'       => '#78350F',
                'verificado' => false,
            ],
            [
                'nome'       => 'Veronica Tia',
                'categoria'  => 'Serviços',
                'cargo'      => 'Serviços Gerais',
                'bio'        => 'Profissional da comunidade. Entre em contato para mais informações.',
                'whatsapp'   => '5521992807538',
                'cor1'       => '#8B5CF6',
                'cor2'       => '#4C1D95',
                'verificado' => false,
            ],
            [
                'nome'       => 'Leka (Alessandra Alves)',
                'categoria'  => 'Serviços',
                'cargo'      => 'Serviços Gerais',
                'bio'        => 'Profissional da comunidade conhecida como Leka.',
                'whatsapp'   => '5521990870805',
                'cor1'       => '#EC4899',
                'cor2'       => '#831843',
                'verificado' => false,
            ],
            [
                'nome'       => 'Acrigel com Decoração',
                'categoria'  => 'Beleza',
                'cargo'      => 'Unhas — Acrigel e Decoração',
                'bio'        => 'Acrigel com decoração R$130 · Simples R$100.',
                'whatsapp'   => '5521992000081',
                'preco_a_partir' => 100.00,
                'cor1'       => '#EC4899',
                'cor2'       => '#FFD23F',
                'verificado' => false,
            ],
            [
                'nome'       => 'Pedicure',
                'categoria'  => 'Beleza',
                'cargo'      => 'Pedicure',
                'bio'        => 'Pedicure a partir de R$30. Agende seu horário!',
                'whatsapp'   => '5521999638942',
                'preco_a_partir' => 30.00,
                'cor1'       => '#F472B6',
                'cor2'       => '#9D174D',
                'verificado' => false,
            ],
            [
                'nome'       => 'Salgadinho e Docinho de Aniversário',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Salgados e Doces para Festas',
                'bio'        => 'Cento de salgadinho e docinho para aniversários e festas.',
                'whatsapp'   => '5521979181057',
                'cor1'       => '#FBBF24',
                'cor2'       => '#92400E',
                'verificado' => false,
            ],
            [
                'nome'       => 'Melhor Açaí',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Açaí',
                'bio'        => 'O melhor açaí da região!',
                'whatsapp'   => '5521998206635',
                'cor1'       => '#7C3AED',
                'cor2'       => '#4C1D95',
                'verificado' => false,
            ],
            [
                'nome'       => 'Venda de Açaí na Praia',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Açaí — Venda Ambulante',
                'bio'        => 'Venda de açaí na praia. Vaga disponível para trabalhar nessa área.',
                'whatsapp'   => '5581990028469',
                'cor1'       => '#7C3AED',
                'cor2'       => '#2563EB',
                'verificado' => false,
            ],
            [
                'nome'       => 'Entrega de Cerveja',
                'categoria'  => 'Alimentação',
                'cargo'      => 'Bebidas e Delivery',
                'bio'        => 'Entrega de cerveja e bebidas por delivery.',
                'whatsapp'   => '5521979050258',
                'cor1'       => '#F59E0B',
                'cor2'       => '#1D4ED8',
                'verificado' => false,
            ],
        ];

        foreach ($profissionais as $dados) {
            Profissional::firstOrCreate(
                ['whatsapp' => $dados['whatsapp'], 'nome' => $dados['nome']],
                array_merge($dados, [
                    'comunidade_id'      => $comunidade?->id,
                    'estrelas'           => 5.0,
                    'total_avaliacoes'   => 0,
                    'total_atendimentos' => 0,
                    'ativo'              => true,
                ])
            );
        }

        $this->command->info('✅ 15 profissionais comunitários inseridos!');
    }
}
