<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static \UnitEnum|string|null $navigationGroup = 'Estrutura';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'usuarios';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados pessoais')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome completo')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('handle')
                        ->label('Apelido (@)')
                        ->prefix('@')
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->tel()
                        ->maxLength(20),
                    Forms\Components\Textarea::make('bio')
                        ->label('Bio')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Acesso e segurança')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Role / Nível de acesso')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Forms\Components\TextInput::make('password')
                        ->label('Senha')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create'),
                    Forms\Components\Toggle::make('ativo')
                        ->label('Usuário ativo')
                        ->default(true),
                    Forms\Components\DateTimePicker::make('verified_at')
                        ->label('Verificado em')
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('handle')
                    ->label('@')
                    ->prefix('@')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'super_admin' => 'danger',
                        'admin'       => 'warning',
                        'moderador'   => 'info',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('verified_at')
                    ->label('Verificado')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->verified_at)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastro')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('ativo')->label('Ativos'),
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name'),
                Tables\Filters\SelectFilter::make('comunidade_id')
                    ->label('Comunidade')
                    ->relationship('comunidade', 'nome'),
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
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
