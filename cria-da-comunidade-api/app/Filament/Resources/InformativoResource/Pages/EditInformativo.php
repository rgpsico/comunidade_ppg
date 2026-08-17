<?php
namespace App\Filament\Resources\InformativoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\InformativoResource;

class EditInformativo extends EditRecord {
    protected static string $resource = InformativoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
}
