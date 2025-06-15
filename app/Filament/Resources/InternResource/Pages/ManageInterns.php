<?php

namespace App\Filament\Resources\InternResource\Pages;

use App\Filament\Resources\InternResource;
use App\Http\Controllers\SyncController;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;

class ManageInterns extends ManageRecords
{
    protected static string $resource = InternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label(__('headings.Sync'))
                ->action(function () {
                    $controller = new SyncController();
                    $controller->syncInterns();

                    Notification::make()
                        ->title('Pasantes sincronizados correctamente')
                        ->success()
                        ->send();
                })
                ->icon('heroicon-o-arrow-path')
                ->color('primary'),
        ];
    }
}
