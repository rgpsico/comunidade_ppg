<?php
namespace App\Filament\Resources\EventoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EventoResource;

class ListEventos extends ListRecords {
    protected static string $resource = EventoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
