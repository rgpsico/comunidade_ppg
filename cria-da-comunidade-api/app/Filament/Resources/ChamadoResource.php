<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChamadoResource\Pages;
use App\Models\Chamado;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ChamadoResource extends Resource
{
    protected static ?string $model = Chamado::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static \UnitEnum|string|null $navigationGroup = 'Comunidade';
    protected static ?string $navigationLabel = 'Chamados';
    protected static ?string $modelLabel = 'Chamado';
    protected static ?string $pluralModelLabel = 'Chamados';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('tipo')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'problema',
                        'success' => 'servico',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'problema' ? 'Problema' : 'Serviço'),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\BadgeColumn::make('urgencia')
                    ->label('Urgência')
                    ->colors([
                        'success' => 'normal',
                        'warning' => 'urgente',
                        'danger'  => 'critico',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'normal'  => 'Normal',
                        'urgente' => 'Urgente',
                        'critico' => 'Crítico',
                        default   => $state,
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'aberto',
                        'warning' => 'aceito',
                        'info'    => 'em_andamento',
                        'success' => 'resolvido',
                        'danger'  => 'cancelado',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'aberto'       => 'Aberto',
                        'aceito'       => 'Aceito',
                        'em_andamento' => 'Em andamento',
                        'resolvido'    => 'Resolvido',
                        'cancelado'    => 'Cancelado',
                        default        => $state,
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Criador')
                    ->searchable(),

                Tables\Columns\TextColumn::make('profissional.nome')
                    ->label('Profissional')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aberto'       => 'Aberto',
                        'aceito'       => 'Aceito',
                        'em_andamento' => 'Em andamento',
                        'resolvido'    => 'Resolvido',
                        'cancelado'    => 'Cancelado',
                    ]),
                Tables\Filters\SelectFilter::make('urgencia')
                    ->options([
                        'normal'  => 'Normal',
                        'urgente' => 'Urgente',
                        'critico' => 'Crítico',
                    ]),
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'problema' => 'Problema',
                        'servico'  => 'Serviço',
                    ]),
            ])
            ->actions([
                Actions\Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Chamado $record) => ! in_array($record->status, ['resolvido', 'cancelado']))
                    ->requiresConfirmation()
                    ->action(fn (Chamado $record) => $record->update(['status' => 'cancelado'])),

                Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChamados::route('/'),
            'view'  => Pages\ViewChamado::route('/{record}'),
        ];
    }
}
