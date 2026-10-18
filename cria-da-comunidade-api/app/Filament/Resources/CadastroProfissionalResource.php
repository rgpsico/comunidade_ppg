<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CadastroProfissionalResource\Pages;
use App\Models\CadastroProfissional;
use App\Models\Profissional;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CadastroProfissionalResource extends Resource
{
    protected static ?string $model = CadastroProfissional::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-plus';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'cadastros-profissionais';
    protected static ?string $modelLabel = 'Cadastro de profissional';
    protected static ?string $pluralModelLabel = 'Cadastros de profissionais';

    /** Bolinha no menu com a quantidade de cadastros novos, ainda sem retorno. */
    public static function getNavigationBadge(): ?string
    {
        $novos = CadastroProfissional::where('status', 'novo')->count();

        return $novos ? (string) $novos : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Quem é')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')->required()->maxLength(100),
                    Forms\Components\TextInput::make('whatsapp')->label('WhatsApp (só dígitos, com DDD)')->required()->maxLength(20),
                    Forms\Components\TextInput::make('email')->label('E-mail (opcional)')->email()->maxLength(150),
                    Forms\Components\Select::make('comunidade_id')->label('Comunidade')->relationship('comunidade', 'nome')->searchable()->preload(),
                ]),
            Section::make('O que faz')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('categoria')->options(array_combine(CadastroProfissional::CATEGORIAS, CadastroProfissional::CATEGORIAS))->required(),
                    Forms\Components\TextInput::make('profissao')->label('Profissão')->required()->maxLength(80),
                    Forms\Components\Textarea::make('descricao')->label('Descrição do serviço')->rows(3)->columnSpanFull(),
                    Forms\Components\Select::make('atende_domicilio')->label('Atende em domicílio?')
                        ->options([1 => 'Sim', 0 => 'Não'])->placeholder('Não se aplica'),
                ]),
            Section::make('Presença digital')
                ->columns(3)
                ->schema([
                    Forms\Components\Toggle::make('tem_site')->label('Tem site'),
                    Forms\Components\Toggle::make('tem_google_meu_negocio')->label('Tem Google Meu Negócio'),
                    Forms\Components\Select::make('nivel_digital')->label('Nível digital')->options(CadastroProfissional::NIVEIS)->required(),
                ]),
            Section::make('Acompanhamento (só você vê)')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')->options(CadastroProfissional::STATUS)->required(),
                    Forms\Components\TextInput::make('origem')->label('Origem do link (?ref=)')->disabled(),
                    Forms\Components\Textarea::make('observacao_admin')->label('Anotações')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->searchable()->weight('bold')
                    ->description(fn (CadastroProfissional $r) => $r->email),
                Tables\Columns\TextColumn::make('profissao')->label('Profissão')->searchable()
                    ->description(fn (CadastroProfissional $r) => $r->categoria),
                Tables\Columns\TextColumn::make('whatsapp')->label('WhatsApp')->searchable()
                    ->url(fn (CadastroProfissional $r) => $r->whatsapp_url, true)->color('success')->icon('heroicon-o-chat-bubble-left-right'),
                Tables\Columns\IconColumn::make('atende_domicilio')->label('Domicílio')->boolean()->placeholder('—'),
                Tables\Columns\IconColumn::make('tem_site')->label('Site')->boolean(),
                Tables\Columns\IconColumn::make('tem_google_meu_negocio')->label('Google')->boolean(),
                Tables\Columns\TextColumn::make('nivel_digital')->label('Digital')->badge()
                    ->formatStateUsing(fn ($state) => CadastroProfissional::NIVEIS[$state] ?? $state)
                    ->color(fn ($state) => $state === 'avancado' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => CadastroProfissional::STATUS[$state] ?? $state)
                    ->color(fn ($state) => match ($state) { 'novo' => 'warning', 'contatado' => 'info', 'aprovado' => 'success', default => 'gray' }),
                Tables\Columns\TextColumn::make('comunidade.nome')->label('Comunidade')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('origem')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('Chegou em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(CadastroProfissional::STATUS),
                Tables\Filters\SelectFilter::make('categoria')->options(array_combine(CadastroProfissional::CATEGORIAS, CadastroProfissional::CATEGORIAS)),
                Tables\Filters\SelectFilter::make('nivel_digital')->label('Nível digital')->options(CadastroProfissional::NIVEIS),
                Tables\Filters\TernaryFilter::make('tem_site')->label('Tem site'),
                Tables\Filters\TernaryFilter::make('tem_google_meu_negocio')->label('Tem Google Meu Negócio'),
                Tables\Filters\TernaryFilter::make('atende_domicilio')->label('Atende em domicílio'),
            ])
            ->actions([
                Actions\Action::make('whatsapp')->label('Chamar')->icon('heroicon-o-chat-bubble-left-right')->color('success')
                    ->url(fn (CadastroProfissional $r) => $r->whatsapp_url . '?text=' . rawurlencode(
                        "Olá, {$r->nome}! Aqui é da Comunidade PPG. Vi o seu cadastro como {$r->profissao} e queria conversar. Podemos falar agora?"
                    ), true),
                Actions\Action::make('contatado')->label('Marcar contatado')->icon('heroicon-o-check')->color('info')
                    ->visible(fn (CadastroProfissional $r) => $r->status === 'novo')
                    ->action(fn (CadastroProfissional $r) => $r->update(['status' => 'contatado'])),
                Actions\Action::make('aprovar')->label('Aprovar e criar perfil')->icon('heroicon-o-check-badge')->color('success')
                    ->visible(fn (CadastroProfissional $r) => $r->status !== 'aprovado')
                    ->requiresConfirmation()
                    ->modalHeading('Criar o perfil no portal?')
                    ->modalDescription('O profissional passa a aparecer na lista de Profissionais da comunidade (sem foto; você pode adicionar depois).')
                    ->action(function (CadastroProfissional $r) {
                        $pro = Profissional::create([
                            'comunidade_id' => $r->comunidade_id,
                            'nome'          => $r->nome,
                            'categoria'     => $r->categoria,
                            'cargo'         => $r->profissao . ($r->atende_domicilio ? ' · Atende em domicílio' : ''),
                            'bio'           => $r->descricao ?: "{$r->profissao} da comunidade.",
                            'whatsapp'      => $r->whatsapp,
                            'tags'          => array_values(array_filter([$r->profissao, $r->atende_domicilio ? 'Domicílio' : null])),
                            'ativo'         => true,
                        ]);
                        $r->update(['status' => 'aprovado', 'profissional_id' => $pro->id]);
                        Notification::make()->title('Perfil criado no portal')->success()->send();
                    }),
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
            'index' => Pages\ListCadastrosProfissionais::route('/'),
            'edit'  => Pages\EditCadastroProfissional::route('/{record}/edit'),
        ];
    }
}
