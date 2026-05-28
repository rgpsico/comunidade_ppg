<?php
namespace App\Filament\Resources\VagaResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\VagaResource;

class ListVagas extends ListRecords {
    protected static string $resource = VagaResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
