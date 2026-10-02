<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuadraResource\Pages;
use App\Filament\Resources\QuadraResource\RelationManagers\TimesRelationManager;
use App\Models\Quadra;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class QuadraResource extends Resource
{
    protected static ?string $model = Quadra::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-map-pin';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Quadra';
    protected static ?string $pluralModelLabel = 'Quadras';
    protected static ?string $navigationLabel = 'Quadras e horários';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados da quadra')
                ->description('Depois de salvar, use a aba "Times e horários" pra cadastrar quem joga em cada dia e hora.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->label('Nome da quadra')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ex: Quadra do Pavão'),
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('endereco')
                        ->label('Endereço / referência')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->placeholder('Ex: Rua principal, ao lado da associação'),
                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('ativo')
                        ->label('Ativa')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('comunidade.nome')->label('Comunidade')->placeholder('—'),
                Tables\Columns\TextColumn::make('times_count')->counts('times')->label('Times / titulares'),
                Tables\Columns\IconColumn::make('ativo')->label('Ativa')->boolean(),
            ])
            ->actions([
                Actions\EditAction::make()->label('Gerenciar'),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nome');
    }

    public static function getRelations(): array
    {
        return [
            TimesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuadras::route('/'),
            'create' => Pages\CreateQuadra::route('/create'),
            'edit'   => Pages\EditQuadra::route('/{record}/edit'),
        ];
    }
}
