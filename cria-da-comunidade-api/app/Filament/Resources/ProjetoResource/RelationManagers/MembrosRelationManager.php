<?php

namespace App\Filament\Resources\ProjetoResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class MembrosRelationManager extends RelationManager
{
    protected static string $relationship = 'membros';
    protected static ?string $title = 'Equipe';
    protected static ?string $modelLabel = 'membro';
    protected static ?string $pluralModelLabel = 'membros da equipe';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('nome')
                ->label('Nome completo')
                ->required()
                ->maxLength(100)
                ->columnSpanFull()
                ->placeholder('Ex: Mestre Fernando Tererê'),

            Forms\Components\TextInput::make('cargo')
                ->label('Cargo / Função')
                ->required()
                ->maxLength(80)
                ->placeholder('Ex: Fundador e Mestre'),

            Forms\Components\ColorPicker::make('cor')
                ->label('Cor do avatar')
                ->default('#FF5E1A'),

            Forms\Components\TextInput::make('ordem')
                ->label('Ordem de exibição')
                ->numeric()
                ->default(0)
                ->minValue(0),

            Forms\Components\Textarea::make('bio')
                ->label('Biografia')
                ->rows(3)
                ->nullable()
                ->columnSpanFull()
                ->placeholder('Breve apresentação do membro da equipe...'),

            Forms\Components\FileUpload::make('foto')
                ->label('Foto')
                ->image()
                ->disk('public')
                ->directory('projetos/equipe')
                ->imageEditor()
                ->maxSize(2048)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('ordem')
            ->defaultSort('ordem')
            ->columns([
                Tables\Columns\ImageColumn::make('foto_url')
                    ->label('')
                    ->width(40)
                    ->height(40)
                    ->defaultImageUrl(fn ($record) => null)
                    ->circular(),
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('cargo')
                    ->label('Cargo')
                    ->color('gray'),
                Tables\Columns\ColorColumn::make('cor')
                    ->label('Cor'),
                Tables\Columns\TextColumn::make('ordem')
                    ->label('#')
                    ->sortable(),
            ])
            ->headerActions([
                Actions\CreateAction::make()->label('+ Adicionar membro'),
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
            ->emptyStateHeading('Equipe não cadastrada')
            ->emptyStateDescription('Adicione os professores, coordenadores e voluntários do projeto.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
