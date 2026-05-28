<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjetoResource\Pages;
use App\Models\Projeto;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProjetoResource extends Resource
{
    protected static ?string $model = Projeto::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-heart';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Projeto Social';
    protected static ?string $pluralModelLabel = 'Projetos Sociais';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do projeto')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->label('Nome do projeto')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('causa')
                        ->label('Causa')
                        ->options([
                            'Educação'   => '📚 Educação',
                            'Esporte'    => '⚽ Esporte',
                            'Cultura'    => '🎭 Cultura',
                            'Assistência'=> '❤ Assistência Social',
                            'Saúde'      => '💊 Saúde',
                            'Música'     => '🎵 Música',
                            'Alimentação'=> '🌱 Alimentação',
                        ])
                        ->required(),
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('responsavel_id')
                        ->label('Responsável')
                        ->relationship('responsavel', 'name')
                        ->searchable()
                        ->nullable(),
                    Forms\Components\TextInput::make('icone')
                        ->label('Ícone (emoji)')
                        ->default('❤')
                        ->maxLength(10),
                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Meta e arrecadação')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('meta')
                        ->label('Meta (R$)')
                        ->numeric()
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('arrecadado')
                        ->label('Arrecadado (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->default(0),
                    Forms\Components\TextInput::make('progresso')
                        ->label('Progresso (%)')
                        ->numeric()
                        ->suffix('%')
                        ->minValue(0)
                        ->maxValue(100)
                        ->default(0),
                    Forms\Components\TextInput::make('impacto_valor')
                        ->label('Número de impacto')
                        ->placeholder('Ex: 180'),
                    Forms\Components\TextInput::make('impacto_label')
                        ->label('Label de impacto')
                        ->placeholder('Ex: alunos ativos'),
                    Forms\Components\TextInput::make('anos_atuando')
                        ->label('Anos atuando')
                        ->numeric()
                        ->default(1),
                ]),

            Section::make('Aparência')
                ->columns(3)
                ->schema([
                    Forms\Components\ColorPicker::make('cor')
                        ->label('Cor do projeto')
                        ->default('#2BD96B'),
                    Forms\Components\TextInput::make('cta_label')
                        ->label('Texto do botão CTA')
                        ->default('Apoiar'),
                    Forms\Components\Toggle::make('ativo')
                        ->label('Projeto ativo')
                        ->default(true),
                ]),

            Section::make('Imagens')
                ->schema([
                    Forms\Components\FileUpload::make('imagem_capa')
                        ->label('Imagem principal')
                        ->image()
                        ->disk('public')
                        ->directory('projetos/capas')
                        ->imageEditor()
                        ->imageCropAspectRatio('16:9')
                        ->maxSize(4096)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('galeria')
                        ->label('Galeria de fotos (atividades, eventos, equipe…)')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('projetos/galeria')
                        ->maxSize(3072)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icone')
                    ->label('')
                    ->width(40),
                Tables\Columns\TextColumn::make('nome')
                    ->label('Projeto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('causa')
                    ->label('Causa')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Educação'    => 'warning',
                        'Esporte'     => 'success',
                        'Cultura'     => 'info',
                        'Assistência' => 'danger',
                        'Saúde'       => 'success',
                        'Música'      => 'warning',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('progresso')
                    ->label('Progresso')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrecadado')
                    ->label('Arrecadado')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('meta')
                    ->label('Meta')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('apoios_count')
                    ->label('Apoiadores')
                    ->counts('apoios')
                    ->sortable(),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('causa')
                    ->options([
                        'Educação'    => 'Educação',
                        'Esporte'     => 'Esporte',
                        'Cultura'     => 'Cultura',
                        'Assistência' => 'Assistência Social',
                        'Saúde'       => 'Saúde',
                        'Música'      => 'Música',
                    ]),
                Tables\Filters\TernaryFilter::make('ativo')->label('Ativos'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nome');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjetos::route('/'),
            'create' => Pages\CreateProjeto::route('/create'),
            'edit'   => Pages\EditProjeto::route('/{record}/edit'),
        ];
    }
}
