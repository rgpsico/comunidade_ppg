<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeladaResource\Pages;
use App\Models\Pelada;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PeladaResource extends Resource
{
    protected static ?string $model = Pelada::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Pelada';
    protected static ?string $pluralModelLabel = 'Peladas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados da pelada')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->required()
                        ->maxLength(120)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('tipo')
                        ->options([
                            'quadra'  => 'Quadra / Futsal',
                            'society' => 'Society',
                            'campo'   => 'Campo',
                            'racha'   => 'Racha',
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
                        ->required(),
                    Forms\Components\TextInput::make('jogadores_linha')
                        ->label('Jogadores na linha')
                        ->numeric()->minValue(2)->maxValue(10)->required(),
                    Forms\Components\Select::make('goleiros_por_time')
                        ->label('Goleiro por time')
                        ->options([0 => 'Sem goleiro', 1 => '1 goleiro'])
                        ->required(),
                    Forms\Components\TextInput::make('tempo_jogo_min')
                        ->label('Tempo de jogo (min)')
                        ->numeric()->minValue(3)->maxValue(120)->required(),
                    Forms\Components\TextInput::make('local')->maxLength(160),
                    Forms\Components\TextInput::make('quando')->maxLength(120),
                    Forms\Components\Toggle::make('ativo')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('tipo')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('jogadores_linha')
                    ->label('Linha')
                    ->formatStateUsing(fn ($state, Pelada $record) => $state . ($record->goleiros_por_time ? ' + 1 gol' : '')),
                Tables\Columns\TextColumn::make('jogadores_count')
                    ->counts('jogadores')
                    ->label('Na lista'),
                Tables\Columns\TextColumn::make('organizador.name')->label('Organizador')->searchable(),
                Tables\Columns\TextColumn::make('comunidade.nome')->label('Comunidade')->placeholder('—'),
                Tables\Columns\IconColumn::make('ativo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Criada em')->dateTime('d/m/Y H:i')->sortable(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeladas::route('/'),
            'edit'  => Pages\EditPelada::route('/{record}/edit'),
        ];
    }
}
