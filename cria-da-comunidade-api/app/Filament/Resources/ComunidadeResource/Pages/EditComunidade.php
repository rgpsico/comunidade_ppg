<?php
namespace App\Filament\Resources\ComunidadeResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ComunidadeResource;

class EditComunidade extends EditRecord {
    protected static string $resource = ComunidadeResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
