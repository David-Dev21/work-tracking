<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use App\Http\Controllers\SyncController;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;

class ManageAreas extends ManageRecords
{
    protected static string $resource = AreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label(__('headings.Sync'))
                ->action(function () {
                    $controller = new SyncController();
                    $controller->syncAreas();

                    Notification::make()
                        ->title('Áreas sincronizadas correctamente')
                        ->success()
                        ->send();
                })
                ->icon('heroicon-o-arrow-path')
                ->color('primary'),
        ];
    }
}
