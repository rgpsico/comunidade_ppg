<?php
namespace App\Filament\Resources\ProjetoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProjetoResource;

class ListProjetos extends ListRecords {
    protected static string $resource = ProjetoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
