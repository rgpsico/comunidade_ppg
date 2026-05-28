<?php
namespace App\Filament\Resources\ProjetoResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProjetoResource;

class CreateProjeto extends CreateRecord {
    protected static string $resource = ProjetoResource::class;
    protected function getRedirectUrl(): string { return static::$resource::getUrl('index'); }
}
