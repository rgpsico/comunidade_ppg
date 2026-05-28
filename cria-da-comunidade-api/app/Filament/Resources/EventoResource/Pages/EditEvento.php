<?php
namespace App\Filament\Resources\EventoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EventoResource;

class EditEvento extends EditRecord {
    protected static string $resource = EventoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
