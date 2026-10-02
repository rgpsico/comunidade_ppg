<?php

namespace App\Filament\Resources\QuadraResource\Pages;

use App\Filament\Resources\QuadraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuadras extends ListRecords
{
    protected static string $resource = QuadraResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
