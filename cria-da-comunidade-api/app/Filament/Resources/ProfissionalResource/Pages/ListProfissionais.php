<?php
namespace App\Filament\Resources\ProfissionalResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProfissionalResource;

class ListProfissionais extends ListRecords {
    protected static string $resource = ProfissionalResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
