<?php

namespace App\Filament\Resources\NotificationToUserResource\Pages;

use App\Filament\Resources\NotificationToUserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificationToUsers extends ListRecords
{
    protected static string $resource = NotificationToUserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
