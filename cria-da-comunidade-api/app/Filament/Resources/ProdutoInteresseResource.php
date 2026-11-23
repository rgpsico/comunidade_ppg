<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LojaResource\RelationManagers\ProdutosRelationManager;
use App\Filament\Resources\ProdutoInteresseResource\Pages;
use App\Models\ProdutoInteresse;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Interessados / vendas: quem pediu contato num produto (botão "Tenho interesse" ou assistente)
 * e, na revenda, com quem buscar o produto.
 */
class ProdutoInteresseResource extends Resource
{
    protected static ?string $model = ProdutoInteresse::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 6;
    protected static ?string $modelLabel = 'Interessado';
    protected static ?string $pluralModelLabel = 'Interessados / vendas';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        $wpp = fn (?string $fone) => ProdutosRelationManager::linkWhatsapp($fone);

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['loja:id,nome', 'produto' => fn ($p) => $p->withTrashed()]))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('produto.nome')
                    ->label('Produto')
                    ->description(fn (ProdutoInteresse $r) => $r->loja?->nome)
                    ->placeholder('(produto removido)')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('nome')
                    ->label('Cliente')
                    ->description(fn (ProdutoInteresse $r) => $r->telefone)
                    ->url(fn (ProdutoInteresse $r) => $wpp($r->telefone), shouldOpenInNewTab: true)
                    ->searchable(['nome', 'telefone']),
                Tables\Columns\TextColumn::make('produto.fornecedor_nome')
                    ->label('Buscar com')
                    ->description(fn (ProdutoInteresse $r) => $r->produto?->fornecedor_contato)
                    ->url(fn (ProdutoInteresse $r) => $wpp($r->produto?->fornecedor_contato), shouldOpenInNewTab: true)
                    ->tooltip(fn (ProdutoInteresse $r) => $r->produto?->fornecedor_obs)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('origem')
                    ->label('Origem')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'assistente' ? 'Assistente' : 'Botão')
                    ->color(fn (string $state) => $state === 'assistente' ? 'success' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('loja_id')->label('Loja')->relationship('loja', 'nome'),
                Tables\Filters\SelectFilter::make('origem')->options(['assistente' => 'Assistente', 'botao' => 'Botão']),
            ])
            ->actions([
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProdutoInteresses::route('/'),
        ];
    }
}
