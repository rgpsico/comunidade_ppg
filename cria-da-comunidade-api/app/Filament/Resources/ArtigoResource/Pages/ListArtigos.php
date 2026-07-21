<?php
namespace App\Filament\Resources\ArtigoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ArtigoResource;

class ListArtigos extends ListRecords {
    protected static string $resource = ArtigoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
