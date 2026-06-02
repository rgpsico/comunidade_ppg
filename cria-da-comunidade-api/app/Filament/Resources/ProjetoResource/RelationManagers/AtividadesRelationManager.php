<?php

namespace App\Filament\Resources\ProjetoResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class AtividadesRelationManager extends RelationManager
{
    protected static string $relationship = 'atividades';
    protected static ?string $title = 'Atividades';
    protected static ?string $modelLabel = 'atividade';
    protected static ?string $pluralModelLabel = 'atividades';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('titulo')
                ->label('Título da atividade')
                ->required()
                ->maxLength(120)
                ->columnSpanFull()
                ->placeholder('Ex: Treino de Jiujitsu Kids'),

            Forms\Components\TextInput::make('dias')
                ->label('Dias da semana')
                ->required()
                ->maxLength(80)
                ->placeholder('Ex: Segunda a Sexta'),

            Forms\Components\TextInput::make('horario')
                ->label('Horário')
                ->required()
                ->maxLength(40)
                ->placeholder('Ex: 18:00 – 19:00'),

            Forms\Components\TextInput::make('vagas')
                ->label('Nº de vagas')
                ->numeric()
                ->nullable()
                ->minValue(0)
                ->placeholder('Deixe em branco se ilimitado'),

            Forms\Components\Textarea::make('descricao')
                ->label('Descrição')
                ->rows(3)
                ->nullable()
                ->columnSpanFull()
                ->placeholder('Descreva a atividade, faixa etária, nível etc.'),

            Forms\Components\TextInput::make('ordem')
                ->label('Ordem de exibição')
                ->numeric()
                ->default(0)
                ->minValue(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('ordem')
            ->defaultSort('ordem')
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Atividade')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('dias')
                    ->label('Dias'),
                Tables\Columns\TextColumn::make('horario')
                    ->label('Horário')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('vagas')
                    ->label('Vagas')
                    ->placeholder('∞'),
                Tables\Columns\TextColumn::make('ordem')
                    ->label('#')
                    ->sortable(),
            ])
            ->headerActions([
                Actions\CreateAction::make()->label('+ Nova atividade'),
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
            ->emptyStateHeading('Nenhuma atividade cadastrada')
            ->emptyStateDescription('Adicione as atividades oferecidas por este projeto.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
