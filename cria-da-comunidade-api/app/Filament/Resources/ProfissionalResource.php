<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfissionalResource\Pages;
use App\Models\Profissional;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProfissionalResource extends Resource
{
    protected static ?string $model = Profissional::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'profissionais';
    protected static ?string $modelLabel = 'Profissional';
    protected static ?string $pluralModelLabel = 'Profissionais';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->label('Nome completo')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('cargo')
                        ->label('Cargo / Especialidade')
                        ->placeholder('Ex: Manicure · Em casa')
                        ->required(),
                    Forms\Components\Select::make('categoria')
                        ->label('Categoria')
                        ->options([
                            'Alimentação' => 'Alimentação',
                            'Beleza'      => 'Beleza',
                            'Casa'        => 'Casa',
                            'Construção'  => 'Construção',
                            'Educação'    => 'Educação',
                            'Eventos'     => 'Eventos',
                            'Pet'         => 'Pet',
                            'Saúde'       => 'Saúde',
                            'Serviços'    => 'Serviços',
                            'Tecnologia'  => 'Tecnologia',
                            'Transporte'  => 'Transporte',
                        ])
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('user_id')
                        ->label('Usuário vinculado')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->nullable(),
                    Forms\Components\TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->tel()
                        ->maxLength(20),
                    Forms\Components\Textarea::make('bio')
                        ->label('Bio / Apresentação')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Valores e métricas')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('preco_a_partir')
                        ->label('Preço a partir de (R$)')
                        ->numeric()
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('estrelas')
                        ->label('Avaliação (estrelas)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(5)
                        ->step(0.1)
                        ->default(5.0),
                    Forms\Components\TextInput::make('total_avaliacoes')
                        ->label('Total de avaliações')
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('total_atendimentos')
                        ->label('Total de atendimentos')
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('tempo_resposta')
                        ->label('Tempo de resposta')
                        ->placeholder('Ex: ~12min'),
                ]),

            Section::make('Aparência e status')
                ->columns(3)
                ->schema([
                    Forms\Components\ColorPicker::make('cor1')
                        ->label('Cor primária do card')
                        ->default('#FF5E1A'),
                    Forms\Components\ColorPicker::make('cor2')
                        ->label('Cor secundária do card')
                        ->default('#FFD23F'),
                    Forms\Components\TagsInput::make('tags')
                        ->label('Tags')
                        ->placeholder('Ex: Atende em casa')
                        ->columnSpan(1),
                    Forms\Components\Toggle::make('verificado')
                        ->label('Profissional verificado ✓')
                        ->default(false),
                    Forms\Components\Toggle::make('ativo')
                        ->label('Perfil ativo')
                        ->default(true),
                ]),

            Section::make('Assinatura Premium')
                ->icon('heroicon-o-star')
                ->description('Profissionais premium aparecem na Home para todos os usuários.')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('plano')
                        ->label('Plano')
                        ->options([
                            'free'    => '🆓 Free — listagem normal',
                            'premium' => '👑 Premium — aparece na Home',
                        ])
                        ->default('free')
                        ->required()
                        ->live(),
                    Forms\Components\DateTimePicker::make('premium_expira_em')
                        ->label('Assinatura válida até')
                        ->helperText('Deixe em branco para não expirar.')
                        ->nullable()
                        ->visible(fn ($get) => $get('plano') === 'premium'),
                ]),

            Section::make('Imagens')
                ->schema([
                    Forms\Components\FileUpload::make('foto')
                        ->label('Foto de perfil')
                        ->image()
                        ->disk('public')
                        ->directory('profissionais/fotos')
                        ->imageEditor()
                        ->imageCropAspectRatio('1:1')
                        ->maxSize(3072)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('galeria')
                        ->label('Galeria de fotos')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('profissionais/galeria')
                        ->maxSize(3072)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->nome).'&background=FF5E1A&color=fff')
                    ->width(36)
                    ->height(36),
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Beleza'     => 'pink',
                        'Construção' => 'warning',
                        'Saúde'      => 'success',
                        'Transporte' => 'info',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('cargo')
                    ->label('Especialidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estrelas')
                    ->label('⭐')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_atendimentos')
                    ->label('Atendimentos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('preco_a_partir')
                    ->label('A partir de')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\IconColumn::make('verificado')
                    ->label('Verificado')
                    ->boolean(),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('plano')
                    ->label('Plano')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => match (true) {
                        $state === 'premium' && $record->is_premium => '👑 Premium',
                        $state === 'premium'                        => '⏰ Expirado',
                        default                                     => 'Free',
                    })
                    ->color(fn ($state, $record) => match (true) {
                        $state === 'premium' && $record->is_premium => 'warning',
                        $state === 'premium'                        => 'gray',
                        default                                     => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'Alimentação' => 'Alimentação',
                        'Beleza'      => 'Beleza',
                        'Casa'        => 'Casa',
                        'Construção'  => 'Construção',
                        'Educação'    => 'Educação',
                        'Eventos'     => 'Eventos',
                        'Pet'         => 'Pet',
                        'Saúde'       => 'Saúde',
                        'Serviços'    => 'Serviços',
                        'Tecnologia'  => 'Tecnologia',
                        'Transporte'  => 'Transporte',
                    ]),
                Tables\Filters\TernaryFilter::make('verificado')->label('Verificados'),
                Tables\Filters\TernaryFilter::make('ativo')->label('Ativos'),
                Tables\Filters\SelectFilter::make('plano')
                    ->label('Plano')
                    ->options(['free' => 'Free', 'premium' => '👑 Premium']),
                Tables\Filters\SelectFilter::make('comunidade_id')
                    ->label('Comunidade')
                    ->relationship('comunidade', 'nome'),
            ])
            ->actions([
                Actions\Action::make('ativar_premium')
                    ->label('👑 Ativar Premium')
                    ->color('warning')
                    ->icon('heroicon-o-star')
                    ->requiresConfirmation()
                    ->modalHeading('Ativar assinatura Premium')
                    ->modalDescription('O profissional passará a aparecer na Home para todos os usuários.')
                    ->action(fn (Profissional $record) => $record->update([
                        'plano'              => 'premium',
                        'premium_expira_em'  => now()->addDays(30),
                    ]))
                    ->visible(fn (Profissional $record) => !$record->is_premium),
                Actions\Action::make('desativar_premium')
                    ->label('Remover Premium')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (Profissional $record) => $record->update([
                        'plano'             => 'free',
                        'premium_expira_em' => null,
                    ]))
                    ->visible(fn (Profissional $record) => $record->is_premium),
                Actions\Action::make('verificar')
                    ->label('Verificar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Profissional $record) => $record->update(['verificado' => true]))
                    ->visible(fn (Profissional $record) => !$record->verificado),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('desativar_todos')
                    ->label('Desativar todos')
                    ->icon('heroicon-o-eye-slash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Desativar TODOS os profissionais')
                    ->modalDescription('Todos os profissionais ficarão ocultos no app. Pode reverter depois.')
                    ->modalSubmitActionLabel('Sim, desativar todos')
                    ->action(fn () => Profissional::query()->update(['ativo' => false])),

                Tables\Actions\Action::make('ativar_todos')
                    ->label('Ativar todos')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ativar TODOS os profissionais')
                    ->modalDescription('Todos os profissionais voltarão a aparecer no app.')
                    ->modalSubmitActionLabel('Sim, ativar todos')
                    ->action(fn () => Profissional::query()->update(['ativo' => true])),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('desativar')
                        ->label('Desativar selecionados')
                        ->icon('heroicon-o-eye-slash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Desativar profissionais')
                        ->modalDescription('Os profissionais selecionados ficarão ocultos no app. Você pode reativar depois.')
                        ->modalSubmitActionLabel('Sim, desativar')
                        ->action(fn ($records) => $records->each->update(['ativo' => false])),

                    Tables\Actions\BulkAction::make('ativar')
                        ->label('Ativar selecionados')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Ativar profissionais')
                        ->modalDescription('Os profissionais selecionados voltarão a aparecer no app.')
                        ->modalSubmitActionLabel('Sim, ativar')
                        ->action(fn ($records) => $records->each->update(['ativo' => true])),

                    Tables\Actions\BulkAction::make('ativar_premium_bulk')
                        ->label('👑 Ativar Premium (30 dias)')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Ativar Premium em lote')
                        ->modalDescription('Os selecionados receberão plano Premium por 30 dias e aparecerão na Home.')
                        ->modalSubmitActionLabel('Ativar Premium')
                        ->action(fn ($records) => $records->each->update([
                            'plano'             => 'premium',
                            'premium_expira_em' => now()->addDays(30),
                        ])),

                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nome');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProfissionais::route('/'),
            'create' => Pages\CreateProfissional::route('/create'),
            'edit'   => Pages\EditProfissional::route('/{record}/edit'),
        ];
    }
}
