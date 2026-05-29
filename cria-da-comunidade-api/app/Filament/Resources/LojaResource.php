<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LojaResource\Pages;
use App\Filament\Resources\LojaResource\RelationManagers\ProdutosRelationManager;
use App\Models\Loja;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LojaResource extends Resource
{
    protected static ?string $model = Loja::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Loja';
    protected static ?string $pluralModelLabel = 'Lojas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Dados da loja')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->label('Nome da loja')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('categoria')
                        ->label('Categoria')
                        ->options([
                            'Alimentação'     => '🍽 Alimentação',
                            'Moda'            => '👗 Moda',
                            'Eletrônicos'     => '💻 Eletrônicos',
                            'Beleza'          => '💄 Beleza',
                            'Casa & Deco'     => '🏠 Casa & Deco',
                            'Mercadinho'      => '🛒 Mercadinho',
                            'Pet'             => '🐾 Pet',
                            'Celular & Acess' => '📱 Celular & Acessórios',
                            'Saúde & Higiene' => '🧴 Saúde & Higiene',
                            'Presentes'       => '🎁 Presentes',
                            'Outros'          => '🏪 Outros',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->placeholder('5521999999999')
                        ->maxLength(20),

                    Forms\Components\TextInput::make('endereco')
                        ->label('Endereço')
                        ->placeholder('Rua X, nº 10, Alemão')
                        ->maxLength(200),

                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('user_id')
                        ->label('Responsável')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->nullable(),

                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Aparência')
                ->columns(2)
                ->schema([
                    Forms\Components\ColorPicker::make('cor1')
                        ->label('Cor principal')
                        ->default('#FF5E1A'),
                    Forms\Components\ColorPicker::make('cor2')
                        ->label('Cor secundária')
                        ->default('#FFD23F'),
                    Forms\Components\FileUpload::make('logo')
                        ->label('Logo da loja')
                        ->image()
                        ->disk('public')
                        ->directory('lojas/logos')
                        ->imageEditor()
                        ->imageCropAspectRatio('1:1')
                        ->maxSize(2048),
                    Forms\Components\FileUpload::make('capa')
                        ->label('Imagem de capa')
                        ->image()
                        ->disk('public')
                        ->directory('lojas/capas')
                        ->imageEditor()
                        ->imageCropAspectRatio('16:9')
                        ->maxSize(4096),
                ]),

            Section::make('Status')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('verificado')
                        ->label('Loja verificada')
                        ->helperText('Exibe selo ✓ na listagem')
                        ->default(false),
                    Forms\Components\Toggle::make('ativo')
                        ->label('Loja ativa')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_url')
                    ->label('')
                    ->circular()
                    ->width(40)
                    ->height(40),
                Tables\Columns\TextColumn::make('nome')
                    ->label('Loja')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->copyable(),
                Tables\Columns\TextColumn::make('produtos_count')
                    ->label('Produtos')
                    ->counts('produtos')
                    ->sortable(),
                Tables\Columns\IconColumn::make('verificado')
                    ->label('✓')
                    ->boolean(),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'Alimentação'     => 'Alimentação',
                        'Moda'            => 'Moda',
                        'Eletrônicos'     => 'Eletrônicos',
                        'Beleza'          => 'Beleza',
                        'Casa & Deco'     => 'Casa & Deco',
                        'Mercadinho'      => 'Mercadinho',
                        'Outros'          => 'Outros',
                    ]),
                Tables\Filters\TernaryFilter::make('ativo')->label('Ativas'),
                Tables\Filters\TernaryFilter::make('verificado')->label('Verificadas'),
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

    public static function getRelations(): array
    {
        return [
            ProdutosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLojas::route('/'),
            'create' => Pages\CreateLoja::route('/create'),
            'edit'   => Pages\EditLoja::route('/{record}/edit'),
        ];
    }
}
