<?php

namespace App\Filament\Resources\QuadraResource\RelationManagers;

use App\Models\QuadraHorario;
use App\Models\QuadraTime;
use Closure;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TimesRelationManager extends RelationManager
{
    protected static string $relationship = 'times';
    protected static ?string $title = 'Times e horários';
    protected static ?string $modelLabel = 'time';
    protected static ?string $pluralModelLabel = 'times';

    public function form(Schema $schema): Schema
    {
        $quadraId = (int) $this->getOwnerRecord()->getKey();

        return $schema->components([
            Forms\Components\TextInput::make('nome')
                ->label('Time ou pessoa titular do horário')
                ->required()
                ->maxLength(255)
                ->columnSpanFull()
                ->placeholder('Ex: Time do Tetra, Horário do Roger'),

            Forms\Components\Textarea::make('descricao')
                ->label('Descrição')
                ->rows(2)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('regras')
                ->label('Regras')
                ->rows(3)
                ->columnSpanFull()
                ->placeholder('Ex: 3 na linha, jogo de 10 min, quem perde sai.'),

            Forms\Components\Toggle::make('ativo')
                ->label('Ativo')
                ->helperText('Times inativos não aparecem no portal e não ocupam horário.')
                ->default(true)
                ->rules([
                    fn (?Model $record): Closure => function (string $attribute, $value, Closure $fail) use ($record, $quadraId) {
                        if (! $value || ! $record || $record->ativo) {
                            return;
                        }
                        foreach ($record->horarios as $h) {
                            $c = QuadraHorario::conflito($quadraId, $h->dia_semana, $h->hora_inicio, $h->hora_fim, $record->id);
                            if ($c) {
                                $fail('Não dá pra reativar: ' . QuadraHorario::mensagemConflito($c));
                                return;
                            }
                        }
                    },
                ]),

            Forms\Components\Repeater::make('horarios')
                ->relationship()
                ->label('Dias e horários autorizados')
                ->helperText('Dois times não podem ter horário sobreposto na mesma quadra e no mesmo dia.')
                ->schema([
                    Forms\Components\Select::make('dia_semana')
                        ->label('Dia')
                        ->options(QuadraHorario::DIAS)
                        ->required()
                        ->native(false),
                    Forms\Components\TimePicker::make('hora_inicio')
                        ->label('Início')
                        ->seconds(false)
                        ->required(),
                    Forms\Components\TimePicker::make('hora_fim')
                        ->label('Fim')
                        ->seconds(false)
                        ->required(),
                ])
                ->columns(3)
                ->minItems(1)
                ->addActionLabel('+ Dia e horário')
                ->columnSpanFull()
                ->rules([
                    fn (?Model $record): Closure => function (string $attribute, $value, Closure $fail) use ($record, $quadraId) {
                        $rows = collect($value ?? [])->values();

                        foreach ($rows as $i => $r) {
                            if (blank($r['dia_semana'] ?? null) || blank($r['hora_inicio'] ?? null) || blank($r['hora_fim'] ?? null)) {
                                continue;
                            }
                            $dia = (int) $r['dia_semana'];
                            $ini = QuadraHorario::hm($r['hora_inicio']);
                            $fim = QuadraHorario::hm($r['hora_fim']);
                            $nome = QuadraHorario::DIAS[$dia] ?? 'Dia';

                            if ($fim <= $ini) {
                                $fail("{$nome}: o fim ({$fim}) precisa ser depois do início ({$ini}).");
                                return;
                            }

                            foreach ($rows as $j => $o) {
                                if ($j <= $i || blank($o['dia_semana'] ?? null) || blank($o['hora_inicio'] ?? null) || blank($o['hora_fim'] ?? null)) {
                                    continue;
                                }
                                if ((int) $o['dia_semana'] === $dia
                                    && $ini < QuadraHorario::hm($o['hora_fim'])
                                    && $fim > QuadraHorario::hm($o['hora_inicio'])) {
                                    $fail("{$nome}: os horários {$ini}–{$fim} e " . QuadraHorario::hm($o['hora_inicio']) . '–' . QuadraHorario::hm($o['hora_fim']) . ' deste time se sobrepõem.');
                                    return;
                                }
                            }

                            $c = QuadraHorario::conflito($quadraId, $dia, $ini, $fim, $record?->id);
                            if ($c) {
                                $fail(QuadraHorario::mensagemConflito($c));
                                return;
                            }
                        }
                    },
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('horarios'))
            ->defaultSort('nome')
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Time / titular')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('horarios_resumo')
                    ->label('Dias e horários')
                    ->state(fn (QuadraTime $record) => $record->resumoHorarios() ?: '—')
                    ->wrap(),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->headerActions([
                Actions\CreateAction::make()->label('+ Novo time / horário'),
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
            ->emptyStateHeading('Nenhum time cadastrado')
            ->emptyStateDescription('Cadastre quem tem horário nesta quadra e em quais dias.')
            ->emptyStateIcon('heroicon-o-clock');
    }
}
