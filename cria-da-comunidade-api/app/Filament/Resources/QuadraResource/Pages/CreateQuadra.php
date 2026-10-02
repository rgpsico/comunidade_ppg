<?php

namespace App\Filament\Resources\QuadraResource\Pages;

use App\Filament\Resources\QuadraResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuadra extends CreateRecord
{
    protected static string $resource = QuadraResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('edit', ['record' => $this->record]);
    }
}
