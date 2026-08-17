<?php
namespace App\Filament\Resources\InformativoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\InformativoResource;

class ListInformativos extends ListRecords {
    protected static string $resource = InformativoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
