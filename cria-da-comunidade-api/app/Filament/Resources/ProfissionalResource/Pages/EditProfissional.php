<?php
namespace App\Filament\Resources\ProfissionalResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProfissionalResource;

class EditProfissional extends EditRecord {
    protected static string $resource = ProfissionalResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
