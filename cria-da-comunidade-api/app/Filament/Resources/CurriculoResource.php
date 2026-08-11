<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculoResource\Pages;
use App\Models\Curriculo;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CurriculoResource extends Resource
{
    protected static ?string $model = Curriculo::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static \UnitEnum|string|null $navigationGroup = 'Plataforma';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Currículo';
    protected static ?string $pluralModelLabel = 'Banco de Currículos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do candidato')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->label('Nome completo')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->maxLength(150),
                    Forms\Components\TextInput::make('telefone')
                        ->label('Telefone / WhatsApp')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('cidade')
                        ->label('Cidade')
                        ->maxLength(100),
                    Forms\Components\Select::make('disponibilidade')
                        ->label('Disponibilidade')
                        ->options([
                            'imediata' => 'Imediata',
                            '30 dias'  => 'Em 30 dias',
                            '60 dias'  => 'Em 60 dias',
                        ])
                        ->default('imediata'),
                ]),

            Section::make('Área e habilidades')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('area_atuacao')
                        ->label('Área de atuação')
                        ->required()
                        ->options([
                            'Beleza e Estética'     => 'Beleza e Estética',
                            'Construção e Reforma'  => 'Construção e Reforma',
                            'Costura e Moda'        => 'Costura e Moda',
                            'Saúde e Bem-estar'     => 'Saúde e Bem-estar',
                            'Educação'              => 'Educação',
                            'Tecnologia'            => 'Tecnologia',
                            'Gastronomia'           => 'Gastronomia',
                            'Transporte e Entregas' => 'Transporte e Entregas',
                            'Eventos'               => 'Eventos',
                            'Limpeza e Doméstica'   => 'Limpeza e Doméstica',
                            'Administração'         => 'Administração',
                            'Comércio'              => 'Comércio',
                            'Arte e Artesanato'     => 'Arte e Artesanato',
                            'Outro'                 => 'Outro',
                        ])
                        ->searchable(),
                    Forms\Components\Select::make('comunidade_id')
                        ->label('Comunidade')
                        ->relationship('comunidade', 'nome')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TagsInput::make('habilidades')
                        ->label('Habilidades')
                        ->placeholder('Adicione habilidades e pressione Enter')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('experiencia')
                        ->label('Experiência')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('PDF e status')
                ->columns(2)
                ->schema([
                    Forms\Components\FileUpload::make('pdf_path')
                        ->label('Currículo em PDF')
                        ->disk('public')
                        ->directory('curriculos/pdfs')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(5120)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('publicado')
                        ->label('Publicado no banco')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('area_atuacao')
                    ->label('Área')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('telefone')
                    ->label('Telefone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cidade')
                    ->label('Cidade')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('disponibilidade')
                    ->label('Disponível')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'imediata' => 'success',
                        '30 dias'  => 'info',
                        '60 dias'  => 'gray',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('comunidade.nome')
                    ->label('Comunidade')
                    ->sortable(),
                Tables\Columns\IconColumn::make('pdf_path')
                    ->label('PDF')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\IconColumn::make('publicado')
                    ->label('Publicado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('area_atuacao')
                    ->label('Área')
                    ->options([
                        'Beleza e Estética'     => 'Beleza e Estética',
                        'Construção e Reforma'  => 'Construção e Reforma',
                        'Costura e Moda'        => 'Costura e Moda',
                        'Saúde e Bem-estar'     => 'Saúde e Bem-estar',
                        'Educação'              => 'Educação',
                        'Tecnologia'            => 'Tecnologia',
                        'Gastronomia'           => 'Gastronomia',
                        'Transporte e Entregas' => 'Transporte e Entregas',
                        'Eventos'               => 'Eventos',
                        'Limpeza e Doméstica'   => 'Limpeza e Doméstica',
                        'Administração'         => 'Administração',
                        'Comércio'              => 'Comércio',
                        'Arte e Artesanato'     => 'Arte e Artesanato',
                        'Outro'                 => 'Outro',
                    ]),
                Tables\Filters\TernaryFilter::make('publicado')->label('Publicado'),
                Tables\Filters\SelectFilter::make('disponibilidade')
                    ->options([
                        'imediata' => 'Imediata',
                        '30 dias'  => 'Em 30 dias',
                        '60 dias'  => 'Em 60 dias',
                    ]),
                Tables\Filters\SelectFilter::make('comunidade_id')
                    ->label('Comunidade')
                    ->relationship('comunidade', 'nome'),
            ])
            ->actions([
                Actions\Action::make('ver_pdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->url(fn (Curriculo $record) => $record->pdf_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Curriculo $record) => (bool) $record->pdf_path),
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
            'index'  => Pages\ListCurriculos::route('/'),
            'create' => Pages\CreateCurriculo::route('/create'),
            'edit'   => Pages\EditCurriculo::route('/{record}/edit'),
        ];
    }
}
