<?php

namespace App\Filament\Resources\LojaResource\Pages;

use App\Filament\Resources\LojaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoja extends EditRecord
{
    protected static string $resource = LojaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
