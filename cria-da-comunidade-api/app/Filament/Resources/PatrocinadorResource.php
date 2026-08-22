<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatrocinadorResource\Pages;
use App\Models\Patrocinador;
use App\Models\Comunidade;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PatrocinadorResource extends Resource
{
    protected static ?string $model = Patrocinador::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';
    protected static \UnitEnum|string|null $navigationGroup = 'Divulgação';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Patrocinador';
    protected static ?string $pluralModelLabel = 'Patrocinadores';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Patrocinador Master')
                ->description('Apenas um patrocinador pode estar ativo por vez. Ao ativar este, os demais serão desativados automaticamente.')
                ->schema([
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->options(Comunidade::where('ativa', true)->pluck('nome', 'id'))
                        ->searchable()
                        ->nullable()
                        ->placeholder('Global (todas as comunidades)'),

                    Forms\Components\TextInput::make('nome')
                        ->label('Nome do patrocinador')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\Textarea::make('texto')
                        ->label('Texto do anúncio')
                        ->rows(3)
                        ->maxLength(300)
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('link_url')
                            ->label('Link de destino')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://...'),

                        Forms\Components\TextInput::make('texto_botao')
                            ->label('Texto do botão')
                            ->default('Saiba mais')
                            ->maxLength(60),
                    ]),

                    Forms\Components\FileUpload::make('imagem_url')
                        ->label('Banner / Imagem')
                        ->image()
                        ->disk('public')
                        ->directory('patrocinadores')
                        ->maxSize(4096)
                        ->columnSpanFull()
                        ->helperText('Recomendado: 1200×400px (proporção 3:1). Máx. 4MB.'),
                ]),

            Section::make('Período de exibição')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('ativo')
                        ->label('Ativo')
                        ->helperText('Ativar desativará qualquer outro patrocinador vigente.')
                        ->default(false),

                    Forms\Components\DateTimePicker::make('publicado_em')
                        ->label('Início')
                        ->native(false)
                        ->nullable(),

                    Forms\Components\DateTimePicker::make('expira_em')
                        ->label('Expira em')
                        ->native(false)
                        ->nullable()
                        ->helperText('Deixe vazio para sem prazo de expiração'),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagem_url')
                    ->label('')
                    ->width(80)
                    ->height(48)
                    ->extraImgAttributes(['style' => 'border-radius:6px;object-fit:cover']),

                Tables\Columns\TextColumn::make('nome')
                    ->label('Patrocinador')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->default('Global')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('expira_em')
                    ->label('Expira em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sem prazo')
                    ->sortable(),
            ])
            ->actions([
                Actions\Action::make('ativar')
                    ->label('Ativar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Patrocinador $record) => ! $record->ativo)
                    ->requiresConfirmation()
                    ->modalHeading('Ativar patrocinador')
                    ->modalDescription(fn (Patrocinador $record) => "Ativar \"{$record->nome}\"? Os demais serão desativados.")
                    ->action(function (Patrocinador $record) {
                        $record->ativo = true;
                        $record->save();
                        Notification::make()->title('Patrocinador ativado!')->success()->send();
                    }),

                Actions\Action::make('desativar')
                    ->label('Desativar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Patrocinador $record) => $record->ativo)
                    ->action(function (Patrocinador $record) {
                        $record->update(['ativo' => false]);
                        Notification::make()->title('Patrocinador desativado.')->success()->send();
                    }),

                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('ativo', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPatrocinadores::route('/'),
            'create' => Pages\CreatePatrocinador::route('/create'),
            'edit'   => Pages\EditPatrocinador::route('/{record}/edit'),
        ];
    }
}
