<?php

namespace App\Filament\Resources\LojaResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProdutosRelationManager extends RelationManager
{
    protected static string $relationship = 'produtos';
    protected static ?string $title = 'Produtos';
    protected static ?string $modelLabel = 'produto';
    protected static ?string $pluralModelLabel = 'produtos';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do produto')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->label('Nome do produto')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('preco')
                        ->label('Preço (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->required()
                        ->minValue(0),

                    Forms\Components\TextInput::make('preco_promocional')
                        ->label('Preço promocional (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->nullable()
                        ->helperText('Deixe vazio se não houver promoção'),

                    Forms\Components\TextInput::make('categoria')
                        ->label('Categoria do produto')
                        ->placeholder('Ex: Marmitas, Lanches, Bebidas')
                        ->maxLength(80),

                    Forms\Components\TextInput::make('ordem')
                        ->label('Ordem de exibição')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('imagens')
                        ->label('Fotos do produto')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('lojas/produtos')
                        ->maxSize(3072)
                        ->maxFiles(6)
                        ->columnSpanFull(),
                ]),

            Section::make('Status')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('disponivel')
                        ->label('Disponível')
                        ->default(true),
                    Forms\Components\Toggle::make('destaque')
                        ->label('Produto em destaque')
                        ->helperText('Aparece primeiro na listagem'),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagem_principal_url')
                    ->label('')
                    ->width(50)
                    ->height(50)
                    ->defaultImageUrl(fn () => null),
                Tables\Columns\TextColumn::make('nome')
                    ->label('Produto')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('preco')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('preco_promocional')
                    ->label('Promoção')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\IconColumn::make('destaque')
                    ->label('★')
                    ->boolean(),
                Tables\Columns\IconColumn::make('disponivel')
                    ->label('Disponível')
                    ->boolean(),
            ])
            ->defaultSort('ordem')
            ->reorderable('ordem')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
