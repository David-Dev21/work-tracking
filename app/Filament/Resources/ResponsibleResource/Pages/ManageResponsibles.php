<?php

namespace App\Filament\Resources\ResponsibleResource\Pages;

use App\Filament\Resources\ResponsibleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Http\Controllers\SyncController;

class ManageResponsibles extends ManageRecords
{
    protected static string $resource = ResponsibleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sincronizar')
                ->action(function () {
                    $controller = new SyncController();
                    $controller->syncResponsibles();
                    $this->notify('success', 'Responsables sincronizados correctamente');
                })
                ->color('primary')
                ->icon('heroicon-o-arrow-path'),
        ];
    }
}
