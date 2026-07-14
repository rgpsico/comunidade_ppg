<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComunidadeResource\Pages;
use App\Models\Comunidade;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ComunidadeResource extends Resource
{
    protected static ?string $model = Comunidade::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-map-pin';
    protected static \UnitEnum|string|null $navigationGroup = 'Estrutura';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Comunidade';
    protected static ?string $pluralModelLabel = 'Comunidades';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados da Comunidade')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('nome')
                            ->label('Nome da comunidade')
                            ->required()
                            ->maxLength(100)
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state, ''))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug da URL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->helperText('URL: /slug/profissionais — sem espaços ou acentos'),
                    ]),
                    Grid::make(3)->schema([
                        Forms\Components\TextInput::make('cidade')
                            ->label('Cidade')
                            ->default('Rio de Janeiro')
                            ->required(),
                        Forms\Components\TextInput::make('estado')
                            ->label('Estado (UF)')
                            ->maxLength(2)
                            ->default('RJ')
                            ->required(),
                        Forms\Components\TextInput::make('cep')
                            ->label('CEP')
                            ->mask('99999-999'),
                    ]),
                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição')
                        ->rows(3),
                    Forms\Components\Toggle::make('ativa')
                        ->label('Comunidade ativa')
                        ->default(true),
                ]),
            Section::make('Geolocalização')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('lat')
                            ->label('Latitude')
                            ->numeric(),
                        Forms\Components\TextInput::make('lng')
                            ->label('Longitude')
                            ->numeric(),
                    ]),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Comunidade')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cidade')
                    ->label('Cidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('UF')
                    ->badge(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Moradores')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('profissionais_count')
                    ->label('Profissionais')
                    ->counts('profissionais')
                    ->sortable(),
                Tables\Columns\IconColumn::make('ativa')
                    ->label('Ativa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('ativa')->label('Ativas'),
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
            'index'  => Pages\ListComunidades::route('/'),
            'create' => Pages\CreateComunidade::route('/create'),
            'edit'   => Pages\EditComunidade::route('/{record}/edit'),
        ];
    }
}
