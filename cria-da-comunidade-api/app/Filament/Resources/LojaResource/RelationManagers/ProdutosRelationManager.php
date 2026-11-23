<?php

namespace App\Filament\Resources\LojaResource\RelationManagers;

use App\Models\Produto;
use App\Services\FacebookService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
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

            Section::make('Para o assistente responder')
                ->columns(3)
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('variacoes')->label('Cores / tamanhos / variações')->maxLength(500)->placeholder('Ex: Preto, Branco e Azul'),
                    Forms\Components\TextInput::make('garantia')->label('Garantia')->maxLength(300)->helperText('Vazio = garantia padrão da loja'),
                    Forms\Components\TextInput::make('estoque')->label('Estoque')->numeric()->minValue(0)->helperText('Vazio = não informado'),
                ]),

            // Dado interno da revenda: não aparece no site nem para o assistente.
            Section::make('🔒 Fornecedor (revenda — só o admin vê)')
                ->description('De quem comprar quando vender. Vai junto no aviso de venda.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('fornecedor_nome')->label('Fornecedor')->maxLength(150)->placeholder('Ex: Loja do Zé — Saara'),
                    Forms\Components\TextInput::make('fornecedor_contato')->label('WhatsApp / telefone')->tel()->maxLength(60),
                    Forms\Components\Textarea::make('fornecedor_obs')->label('Observação')->rows(2)->maxLength(500)
                        ->placeholder('Endereço, preço de custo, horário…')->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('fornecedor_nome')
                    ->label('Fornecedor')
                    ->description(fn (Produto $record) => $record->fornecedor_contato)
                    ->url(fn (Produto $record) => self::linkWhatsapp($record->fornecedor_contato), shouldOpenInNewTab: true)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('destaque')
                    ->label('★')
                    ->boolean(),
                Tables\Columns\IconColumn::make('disponivel')
                    ->label('Disponível')
                    ->boolean(),
            ])
            ->defaultSort('ordem')
            ->reorderable('ordem')
            ->filters([
                Tables\Filters\SelectFilter::make('disponivel')
                    ->label('Status')
                    ->options([
                        '1' => 'Disponível',
                        '0' => 'Indisponível',
                    ])
                    ->placeholder('Todos'),

                Tables\Filters\SelectFilter::make('destaque')
                    ->label('Destaque')
                    ->options([
                        '1' => 'Em destaque',
                        '0' => 'Sem destaque',
                    ])
                    ->placeholder('Todos'),

                Tables\Filters\Filter::make('categoria')
                    ->form([
                        Forms\Components\TextInput::make('categoria')
                            ->label('Categoria')
                            ->placeholder('Ex: Marmitas, Lanches…'),
                    ])
                    ->query(fn ($query, array $data) =>
                        $query->when(
                            $data['categoria'] ?? null,
                            fn ($q, $v) => $q->where('categoria', 'like', "%{$v}%")
                        )
                    )
                    ->indicateUsing(fn (array $data) =>
                        ($data['categoria'] ?? null) ? 'Categoria: ' . $data['categoria'] : null
                    ),
            ])
            ->filtersFormColumns(3)
            ->actions([
                // O fornecedor fica em $hidden no model (fora da API pública); sem isto o formulário abriria vazio.
                Actions\EditAction::make()
                    ->mutateRecordDataUsing(fn (array $data, Produto $record) => [...$data, ...$record->only(Produto::CAMPOS_FORNECEDOR)]),
                Actions\Action::make('publicar_facebook')
                    ->label('Divulgar')
                    ->icon('heroicon-o-share')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Publicar na Divulga PPG')
                    ->modalDescription(fn (Produto $record) => "Publicar \"{$record->nome}\" na página do Facebook Divulga PPG?")
                    ->modalSubmitActionLabel('Sim, publicar')
                    ->visible(fn (Produto $record) => $record->disponivel)
                    ->action(function (Produto $record) {
                        $loja = $record->loja;
                        $resultado = app(FacebookService::class)->publicarProduto(
                            $record->id,
                            $record->nome,
                            $record->descricao ?? '',
                            $record->preco,
                            $record->preco_promocional,
                            $loja?->nome ?? '',
                        );

                        if ($resultado['sucesso']) {
                            Notification::make()
                                ->title('Publicado no Facebook!')
                                ->body("O produto \"{$record->nome}\" foi divulgado na página.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Erro ao publicar')
                                ->body($resultado['erro'])
                                ->danger()
                                ->send();
                        }
                    }),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->bulkActions([
                Actions\BulkAction::make('ativar')
                    ->label('Ativar selecionados')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ativar produtos selecionados')
                    ->modalDescription('Todos os produtos selecionados ficarão disponíveis na loja.')
                    ->action(fn ($records) => $records->each->update(['disponivel' => true]))
                    ->deselectRecordsAfterCompletion(),

                Actions\BulkAction::make('desativar')
                    ->label('Desativar selecionados')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Desativar produtos selecionados')
                    ->modalDescription('Todos os produtos selecionados ficarão ocultos na loja.')
                    ->action(fn ($records) => $records->each->update(['disponivel' => false]))
                    ->deselectRecordsAfterCompletion(),

                Actions\BulkAction::make('excluir')
                    ->label('Excluir selecionados')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Excluir produtos selecionados')
                    ->modalDescription('Esta ação não pode ser desfeita. Todos os produtos selecionados serão excluídos permanentemente.')
                    ->action(fn ($records) => $records->each->delete())
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function linkWhatsapp(?string $fone): ?string
    {
        $d = preg_replace('/\D/', '', (string) $fone);

        return $d ? 'https://wa.me/' . (strlen($d) <= 11 ? '55' : '') . $d : null;
    }
}
