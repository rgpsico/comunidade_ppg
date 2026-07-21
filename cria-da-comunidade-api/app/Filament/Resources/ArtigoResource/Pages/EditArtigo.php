<?php
namespace App\Filament\Resources\ArtigoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ArtigoResource;

class EditArtigo extends EditRecord {
    protected static string $resource = ArtigoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
}
