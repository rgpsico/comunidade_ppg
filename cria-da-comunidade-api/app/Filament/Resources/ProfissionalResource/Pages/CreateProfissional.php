<?php
namespace App\Filament\Resources\ProfissionalResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProfissionalResource;

class CreateProfissional extends CreateRecord {
    protected static string $resource = ProfissionalResource::class;
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
