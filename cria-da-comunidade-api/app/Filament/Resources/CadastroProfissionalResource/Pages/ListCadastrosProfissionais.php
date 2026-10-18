<?php

namespace App\Filament\Resources\CadastroProfissionalResource\Pages;

use App\Filament\Resources\CadastroProfissionalResource;
use App\Models\Comunidade;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCadastrosProfissionais extends ListRecords
{
    protected static string $resource = CadastroProfissionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('link')
                ->label('Link para enviar aos profissionais')
                ->icon('heroicon-o-link')
                ->modalHeading('Link do cadastro rápido')
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar')
                ->modalContent(fn () => view('filament.cadastro-link', [
                    'base'        => rtrim((string) config('app.frontend_url'), '/'),
                    'comunidades' => Comunidade::where('ativa', true)->orderBy('nome')->get(['nome', 'slug']),
                ])),
        ];
    }
}
