<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InformativoResource\Pages;
use App\Models\Informativo;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class InformativoResource extends Resource
{
    protected static ?string $model = Informativo::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 6;
    protected static ?string $modelLabel = 'Informativo';
    protected static ?string $pluralModelLabel = 'Informativos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informativo')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('fonte')
                        ->label('Fonte / Órgão')
                        ->options([
                            'Águas do Rio'   => 'Águas do Rio',
                            'Light'          => 'Light',
                            'Defesa Civil'   => 'Defesa Civil',
                            'Prefeitura RJ'  => 'Prefeitura RJ',
                            'UPP'            => 'UPP',
                            'Associação'     => 'Associação de Moradores',
                            'Outro'          => 'Outro',
                        ])
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('data_ocorrencia')
                        ->label('Data de ocorrência')
                        ->displayFormat('d/m/Y')
                        ->default(now()),

                    Forms\Components\Textarea::make('corpo')
                        ->label('Texto do informativo')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('urgente')
                        ->label('Urgente')
                        ->helperText('Destaca o informativo com badge vermelho')
                        ->default(false),

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
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(60),

                Tables\Columns\TextColumn::make('fonte')
                    ->label('Fonte')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Águas do Rio'  => 'info',
                        'Light'         => 'warning',
                        'Defesa Civil'  => 'danger',
                        'UPP'           => 'gray',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('data_ocorrencia')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('urgente')
                    ->label('Urgente')
                    ->boolean(),

                Tables\Columns\IconColumn::make('publicado')
                    ->label('Publicado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fonte')
                    ->options([
                        'Águas do Rio'  => 'Águas do Rio',
                        'Light'         => 'Light',
                        'Defesa Civil'  => 'Defesa Civil',
                        'Prefeitura RJ' => 'Prefeitura RJ',
                        'UPP'           => 'UPP',
                        'Associação'    => 'Associação de Moradores',
                        'Outro'         => 'Outro',
                    ]),
                Tables\Filters\TernaryFilter::make('urgente')->label('Urgentes'),
                Tables\Filters\TernaryFilter::make('publicado')->label('Publicados'),
            ])
            ->actions([
                Actions\Action::make('urgente')
                    ->label(fn (Informativo $record) => $record->urgente ? 'Remover urgência' : 'Marcar urgente')
                    ->icon('heroicon-o-fire')
                    ->color('danger')
                    ->action(fn (Informativo $record) => $record->update(['urgente' => !$record->urgente])),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInformativos::route('/'),
            'create' => Pages\CreateInformativo::route('/create'),
            'edit'   => Pages\EditInformativo::route('/{record}/edit'),
        ];
    }
}
