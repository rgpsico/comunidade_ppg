<?php
namespace App\Filament\Resources\VagaResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\VagaResource;

class CreateVaga extends CreateRecord {
    protected static string $resource = VagaResource::class;
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
