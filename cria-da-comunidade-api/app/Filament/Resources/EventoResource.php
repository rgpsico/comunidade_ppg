<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventoResource\Pages;
use App\Models\Evento;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EventoResource extends Resource
{
    protected static ?string $model = Evento::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Evento';
    protected static ?string $pluralModelLabel = 'Eventos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do evento')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('titulo')
                        ->label('Título do evento')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('categoria')
                        ->label('Categoria')
                        ->options([
                            'baile'    => '🎵 Baile',
                            'pagode'   => '🥁 Pagode',
                            'esporte'  => '⚽ Esporte',
                            'cultura'  => '🎭 Cultura',
                            'festa'    => '🎊 Festa',
                            'workshop' => '📚 Workshop',
                            'negócios' => '💼 Negócios',
                            'educação' => '🏫 Educação',
                        ])
                        ->required(),
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('organizador_id')
                        ->label('Organizador')
                        ->relationship('organizador', 'name')
                        ->searchable()
                        ->nullable(),
                    Forms\Components\DateTimePicker::make('data_hora')
                        ->label('Data e hora')
                        ->required()
                        ->timezone('America/Sao_Paulo'),
                    Forms\Components\TextInput::make('local')
                        ->label('Local / Endereço')
                        ->maxLength(200),
                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Ingressos e restrições')
                ->columns(3)
                ->schema([
                    Forms\Components\Toggle::make('gratuito')
                        ->label('Entrada gratuita')
                        ->default(true)
                        ->live(),
                    Forms\Components\TextInput::make('preco')
                        ->label('Preço (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->hidden(fn (Get $get) => $get('gratuito')),
                    Forms\Components\TextInput::make('idade_minima')
                        ->label('Idade mínima')
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('duracao')
                        ->label('Duração estimada')
                        ->placeholder('Ex: ~5h'),
                ]),

            Section::make('Aparência e destaque')
                ->columns(3)
                ->schema([
                    Forms\Components\ColorPicker::make('cor1')
                        ->label('Cor primária')
                        ->default('#FF5E1A'),
                    Forms\Components\ColorPicker::make('cor2')
                        ->label('Cor secundária')
                        ->default('#FFD23F'),
                    Forms\Components\Toggle::make('destaque')
                        ->label('Evento em destaque')
                        ->default(false),
                    Forms\Components\Toggle::make('ativo')
                        ->label('Evento ativo')
                        ->default(true),
                ]),

            Section::make('Imagens')
                ->schema([
                    Forms\Components\FileUpload::make('imagem_capa')
                        ->label('Imagem de capa')
                        ->image()
                        ->disk('public')
                        ->directory('eventos/capas')
                        ->imageEditor()
                        ->imageCropAspectRatio('16:9')
                        ->maxSize(4096)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('galeria')
                        ->label('Galeria de fotos')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('eventos/galeria')
                        ->maxSize(3072)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Evento')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),
                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        'baile', 'festa' => 'danger',
                        'pagode'         => 'warning',
                        'esporte'        => 'success',
                        'cultura'        => 'info',
                        'workshop'       => 'gray',
                        default          => 'gray',
                    }),
                Tables\Columns\TextColumn::make('data_hora')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('local')
                    ->label('Local')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('confirmados')
                    ->label('Confirmados')
                    ->sortable(),
                Tables\Columns\IconColumn::make('gratuito')
                    ->label('Grátis')
                    ->boolean(),
                Tables\Columns\IconColumn::make('destaque')
                    ->label('Destaque')
                    ->boolean(),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'baile'    => 'Baile',
                        'pagode'   => 'Pagode',
                        'esporte'  => 'Esporte',
                        'cultura'  => 'Cultura',
                        'festa'    => 'Festa',
                        'workshop' => 'Workshop',
                    ]),
                Tables\Filters\TernaryFilter::make('destaque')->label('Destaques'),
                Tables\Filters\TernaryFilter::make('ativo')->label('Ativos'),
                Tables\Filters\TernaryFilter::make('gratuito')->label('Gratuitos'),
            ])
            ->actions([
                Actions\Action::make('destacar')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Evento $record) => $record->update(['destaque' => !$record->destaque]))
                    ->label(fn (Evento $record) => $record->destaque ? 'Remover destaque' : 'Destacar'),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('data_hora', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEventos::route('/'),
            'create' => Pages\CreateEvento::route('/create'),
            'edit'   => Pages\EditEvento::route('/{record}/edit'),
        ];
    }
}
