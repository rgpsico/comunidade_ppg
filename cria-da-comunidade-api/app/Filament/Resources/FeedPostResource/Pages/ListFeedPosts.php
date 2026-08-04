<?php
namespace App\Filament\Resources\FeedPostResource\Pages;
use App\Filament\Resources\FeedPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListFeedPosts extends ListRecords {
    protected static string $resource = FeedPostResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
