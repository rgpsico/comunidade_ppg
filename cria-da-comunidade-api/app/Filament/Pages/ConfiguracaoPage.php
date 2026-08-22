<?php

namespace App\Filament\Pages;

use App\Models\Configuracao;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConfiguracaoPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paint-brush';
    protected static \UnitEnum|string|null $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Visual da Plataforma';
    protected static ?string $title = 'Configurações Visuais';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.configuracao';

    public ?array $data = [];

    public function mount(): void
    {
        $config = Configuracao::whereNull('comunidade_id')->first();
        $defaults = Configuracao::defaults();
        $values = $config ? array_merge($defaults, $config->toArray()) : $defaults;
        $this->form->fill($values);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Identidade')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nome_plataforma')
                            ->label('Nome da plataforma')
                            ->required()
                            ->maxLength(120)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('logo_url')
                            ->label('Logotipo')
                            ->image()
                            ->disk('public')
                            ->directory('configuracoes')
                            ->maxSize(2048)
                            ->helperText('PNG ou SVG, fundo transparente. Máx. 2MB.'),

                        Forms\Components\FileUpload::make('favicon_url')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('configuracoes')
                            ->maxSize(512)
                            ->helperText('32×32px ou 64×64px. PNG ou ICO.'),
                    ]),

                Section::make('Cores')
                    ->description('Estas cores serão aplicadas dinamicamente em toda a plataforma via variáveis CSS.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\ColorPicker::make('cor_primaria')
                            ->label('Cor primária (botões, destaques)'),

                        Forms\Components\ColorPicker::make('cor_secundaria')
                            ->label('Cor secundária (acentos)'),

                        Forms\Components\ColorPicker::make('cor_destaque')
                            ->label('Cor de destaque (sucesso)'),

                        Forms\Components\ColorPicker::make('cor_fundo')
                            ->label('Cor de fundo'),

                        Forms\Components\ColorPicker::make('cor_card')
                            ->label('Cor dos cards'),

                        Forms\Components\ColorPicker::make('cor_texto')
                            ->label('Cor do texto principal'),

                        Forms\Components\ColorPicker::make('cor_muted')
                            ->label('Cor do texto secundário'),
                    ]),

                Section::make('Layout')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('listagem_tipo')
                            ->label('Tipo de listagem padrão')
                            ->options(['grade' => 'Grade (cards)', 'lista' => 'Lista'])
                            ->required(),

                        Forms\Components\TextInput::make('itens_por_pagina')
                            ->label('Itens por página')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(100)
                            ->required(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Remove campos não preenchidos para não sobrescrever com null
        $data = array_filter($data, fn ($v) => $v !== null && $v !== '');

        Configuracao::updateOrCreate(
            ['comunidade_id' => null],
            $data
        );

        Notification::make()
            ->title('Configurações salvas!')
            ->body('As alterações serão aplicadas na próxima visita.')
            ->success()
            ->send();
    }
}
