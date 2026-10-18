<?php

namespace App\Filament\Resources\CadastroProfissionalResource\Pages;

use App\Filament\Resources\CadastroProfissionalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCadastroProfissional extends EditRecord
{
    protected static string $resource = CadastroProfissionalResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
