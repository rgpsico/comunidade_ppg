<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtigoResource\Pages;
use App\Models\Artigo;
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

class ArtigoResource extends Resource
{
    protected static ?string $model = Artigo::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';
    protected static \UnitEnum|string|null $navigationGroup = 'Conteúdo';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Artigo';
    protected static ?string $pluralModelLabel = 'Artigos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informações do artigo')
                ->schema([
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->options(Comunidade::where('ativa', true)->pluck('nome', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->live(debounce: 500)
                        ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL amigável)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('Ex: meu-primeiro-artigo — aparece na URL'),

                    Grid::make(2)->schema([
                        Forms\Components\Select::make('categoria')
                            ->label('Categoria')
                            ->options([
                                'Notícia'     => 'Notícia',
                                'Saúde'       => 'Saúde',
                                'Educação'    => 'Educação',
                                'Cultura'     => 'Cultura',
                                'Esporte'     => 'Esporte',
                                'Economia'    => 'Economia',
                                'Tecnologia'  => 'Tecnologia',
                            ])
                            ->default('Notícia')
                            ->required(),

                        Forms\Components\TextInput::make('autor')
                            ->label('Autor')
                            ->maxLength(120)
                            ->placeholder('Redação Cria da Comunidade'),
                    ]),

                    Forms\Components\Textarea::make('resumo')
                        ->label('Resumo (aparece nos cards)')
                        ->rows(2)
                        ->maxLength(300),

                    Forms\Components\TextInput::make('imagem_capa_url')
                        ->label('URL da imagem de capa')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://...'),
                ]),

            Section::make('Conteúdo')
                ->schema([
                    Forms\Components\RichEditor::make('corpo')
                        ->label('Corpo do artigo')
                        ->required()
                        ->columnSpanFull()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3',
                            'bulletList', 'orderedList', 'blockquote',
                            'link', 'redo', 'undo',
                        ]),
                ]),

            Section::make('Publicação')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Toggle::make('publicado')
                            ->label('Publicado')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $set('publicado_em', now());
                                }
                            }),

                        Forms\Components\DateTimePicker::make('publicado_em')
                            ->label('Data de publicação')
                            ->native(false),
                    ]),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagem_capa_url')
                    ->label('')
                    ->width(56)
                    ->height(40)
                    ->defaultImageUrl(fn () => null)
                    ->extraImgAttributes(['style' => 'border-radius:6px;object-fit:cover']),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge(),

                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),

                Tables\Columns\TextColumn::make('autor')
                    ->label('Autor')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('publicado')
                    ->label('Publicado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('publicado_em')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('publicado')->label('Publicados'),
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'Notícia' => 'Notícia', 'Saúde' => 'Saúde',
                        'Educação' => 'Educação', 'Cultura' => 'Cultura',
                        'Esporte' => 'Esporte', 'Economia' => 'Economia',
                        'Tecnologia' => 'Tecnologia',
                    ]),
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
            ->defaultSort('publicado_em', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArtigos::route('/'),
            'create' => Pages\CreateArtigo::route('/create'),
            'edit'   => Pages\EditArtigo::route('/{record}/edit'),
        ];
    }
}
