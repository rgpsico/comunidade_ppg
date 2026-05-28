<?php
namespace App\Filament\Resources\EventoResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EventoResource;

class CreateEvento extends CreateRecord {
    protected static string $resource = EventoResource::class;
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
