<?php
namespace App\Filament\Resources\VagaResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\VagaResource;

class EditVaga extends EditRecord {
    protected static string $resource = VagaResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
