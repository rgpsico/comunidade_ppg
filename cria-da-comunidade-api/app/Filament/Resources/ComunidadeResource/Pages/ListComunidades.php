<?php
namespace App\Filament\Resources\ComunidadeResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ComunidadeResource;

class ListComunidades extends ListRecords {
    protected static string $resource = ComunidadeResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
