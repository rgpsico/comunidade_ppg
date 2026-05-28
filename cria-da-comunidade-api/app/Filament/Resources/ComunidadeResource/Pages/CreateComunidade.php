<?php
namespace App\Filament\Resources\ComunidadeResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ComunidadeResource;

class CreateComunidade extends CreateRecord {
    protected static string $resource = ComunidadeResource::class;
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
