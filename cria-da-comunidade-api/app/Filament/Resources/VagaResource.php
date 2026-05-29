<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VagaResource\Pages;
use App\Models\Vaga;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VagaResource extends Resource
{
    protected static ?string $model = Vaga::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Vaga';
    protected static ?string $pluralModelLabel = 'Vagas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados da vaga')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('titulo')
                        ->label('Título da vaga')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('empresa')
                        ->label('Empresa / Anunciante')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Select::make('tipo')
                        ->label('Tipo de contratação')
                        ->options([
                            'CLT'        => 'CLT',
                            'PJ'         => 'PJ',
                            'Freelance'  => 'Freelance',
                            'Estágio'    => 'Estágio',
                            'Voluntário' => 'Voluntário',
                        ])
                        ->required(),
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('local')
                        ->label('Local')
                        ->placeholder('Ex: São Paulo - SP (Híbrido)'),
                    Forms\Components\TextInput::make('salario')
                        ->label('Salário / Remuneração')
                        ->placeholder('Ex: R$ 1.800'),
                    Forms\Components\Select::make('salario_periodo')
                        ->label('Período do salário')
                        ->options([
                            'hora'   => 'por hora',
                            'dia'    => 'por dia',
                            'semana' => 'por semana',
                            'mês'    => 'por mês',
                            'ano'    => 'por ano',
                        ]),
                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição da vaga')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Requisitos')
                ->schema([
                    Forms\Components\Repeater::make('requisitos')
                        ->relationship()
                        ->label('Requisitos')
                        ->schema([
                            Forms\Components\TextInput::make('descricao')
                                ->label('Requisito')
                                ->required()
                                ->columnSpan(3),
                            Forms\Components\Select::make('nivel')
                                ->label('Nível')
                                ->options([
                                    'obrigatório' => 'Obrigatório',
                                    'desejável'   => 'Desejável',
                                    'opcional'    => 'Opcional',
                                ])
                                ->default('obrigatório')
                                ->columnSpan(1),
                        ])
                        ->columns(4)
                        ->addActionLabel('+ Requisito')
                        ->reorderable('ordem')
                        ->collapsible(),
                ]),

            Section::make('Benefícios')
                ->schema([
                    Forms\Components\Repeater::make('beneficios')
                        ->relationship()
                        ->label('Benefícios')
                        ->schema([
                            Forms\Components\TextInput::make('descricao')
                                ->label('Benefício')
                                ->required(),
                        ])
                        ->addActionLabel('+ Benefício')
                        ->reorderable('ordem')
                        ->collapsible(),
                ]),

            Section::make('Contato do anunciante')
                ->description('Formas de contato que serão exibidas na plataforma para os candidatos.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->placeholder('Ex: 11999990000')
                        ->helperText('Apenas números, com DDD. Ex: 11999990000')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('email_contato')
                        ->label('E-mail de contato')
                        ->email()
                        ->placeholder('Ex: rh@empresa.com.br')
                        ->maxLength(150),
                ]),

            Section::make('Aparência e status')
                ->columns(3)
                ->schema([
                    Forms\Components\ColorPicker::make('logo_cor')
                        ->label('Cor do logo')
                        ->default('#FFD23F'),
                    Forms\Components\TextInput::make('logo_iniciais')
                        ->label('Iniciais do logo')
                        ->maxLength(5)
                        ->placeholder('Ex: PQ'),
                    Forms\Components\Toggle::make('urgente')
                        ->label('Vaga urgente')
                        ->default(false),
                    Forms\Components\Toggle::make('ativa')
                        ->label('Vaga ativa')
                        ->default(true),
                ]),

            Section::make('Logo da empresa')
                ->description('Se enviar imagem, ela substitui as iniciais coloridas.')
                ->schema([
                    Forms\Components\FileUpload::make('logo_imagem')
                        ->label('Logo (imagem)')
                        ->image()
                        ->disk('public')
                        ->directory('vagas/logos')
                        ->imageEditor()
                        ->imageCropAspectRatio('1:1')
                        ->maxSize(2048)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Vaga')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('empresa')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'CLT'        => 'success',
                        'PJ'         => 'info',
                        'Freelance'  => 'warning',
                        'Estágio'    => 'gray',
                        'Voluntário' => 'gray',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('salario')
                    ->label('Salário')
                    ->searchable(),
                Tables\Columns\TextColumn::make('candidatos')
                    ->label('Candidatos')
                    ->sortable(),
                Tables\Columns\IconColumn::make('urgente')
                    ->label('Urgente')
                    ->boolean(),
                Tables\Columns\IconColumn::make('ativa')
                    ->label('Ativa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Publicada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'CLT'        => 'CLT',
                        'PJ'         => 'PJ',
                        'Freelance'  => 'Freelance',
                        'Estágio'    => 'Estágio',
                        'Voluntário' => 'Voluntário',
                    ]),
                Tables\Filters\TernaryFilter::make('urgente')->label('Urgentes'),
                Tables\Filters\TernaryFilter::make('ativa')->label('Ativas'),
                Tables\Filters\SelectFilter::make('comunidade_id')
                    ->label('Comunidade')
                    ->relationship('comunidade', 'nome'),
            ])
            ->actions([
                Actions\Action::make('urgente')
                    ->label(fn (Vaga $record) => $record->urgente ? 'Remover urgência' : 'Marcar urgente')
                    ->icon('heroicon-o-fire')
                    ->color('danger')
                    ->action(fn (Vaga $record) => $record->update(['urgente' => !$record->urgente])),
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
            'index'  => Pages\ListVagas::route('/'),
            'create' => Pages\CreateVaga::route('/create'),
            'edit'   => Pages\EditVaga::route('/{record}/edit'),
        ];
    }
}
