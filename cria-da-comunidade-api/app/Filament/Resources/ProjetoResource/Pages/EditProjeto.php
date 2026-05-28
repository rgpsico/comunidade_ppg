<?php
namespace App\Filament\Resources\ProjetoResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjetoResource;

class EditProjeto extends EditRecord {
    protected static string $resource = ProjetoResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
