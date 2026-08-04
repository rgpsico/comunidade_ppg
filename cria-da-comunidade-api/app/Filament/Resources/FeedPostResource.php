<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedPostResource\Pages;
use App\Models\Comunidade;
use App\Models\FeedPost;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FeedPostResource extends Resource
{
    protected static ?string $model = FeedPost::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Post do Feed';
    protected static ?string $pluralModelLabel = 'Feed da Comunidade';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post')->schema([
                Forms\Components\Select::make('comunidade_id')
                    ->label('Comunidade')
                    ->options(Comunidade::where('ativa', true)->pluck('nome', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('autor')
                    ->label('Autor / Nome')
                    ->required()
                    ->maxLength(120)
                    ->placeholder('Ex: MC Bravão, ONG Raízes...'),

                Forms\Components\TextInput::make('legenda')
                    ->label('Legenda')
                    ->maxLength(200),

                Forms\Components\TextInput::make('imagem_url')
                    ->label('URL da imagem')
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://... (deixe vazio para usar gradiente)'),

                Grid::make(3)->schema([
                    Forms\Components\ColorPicker::make('cor1')
                        ->label('Cor 1 (gradiente)')
                        ->default('#FF5E1A'),

                    Forms\Components\ColorPicker::make('cor2')
                        ->label('Cor 2 (gradiente)')
                        ->default('#FFD23F'),

                    Forms\Components\Select::make('tamanho')
                        ->label('Tamanho no grid')
                        ->options([
                            'normal' => 'Normal (1×1)',
                            'tall'   => 'Alto (1×2)',
                            'wide'   => 'Largo (2×1)',
                        ])
                        ->default('normal')
                        ->required(),
                ]),

                Forms\Components\Toggle::make('publicado')
                    ->label('Publicado')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagem_url')
                    ->label('')
                    ->width(56)->height(40)
                    ->defaultImageUrl(fn () => null)
                    ->extraImgAttributes(['style' => 'border-radius:6px;object-fit:cover']),

                Tables\Columns\TextColumn::make('autor')
                    ->label('Autor')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('legenda')
                    ->label('Legenda')
                    ->limit(50),

                Tables\Columns\TextColumn::make('tamanho')
                    ->label('Tamanho')
                    ->badge(),

                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade'),

                Tables\Columns\IconColumn::make('publicado')
                    ->label('Ativo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('publicado')->label('Publicados'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFeedPosts::route('/'),
            'create' => Pages\CreateFeedPost::route('/create'),
            'edit'   => Pages\EditFeedPost::route('/{record}/edit'),
        ];
    }
}
