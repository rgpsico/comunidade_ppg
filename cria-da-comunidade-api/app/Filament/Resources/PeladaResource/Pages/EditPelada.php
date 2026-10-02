<?php

namespace App\Filament\Resources\PeladaResource\Pages;

use App\Filament\Resources\PeladaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPelada extends EditRecord
{
    protected static string $resource = PeladaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
