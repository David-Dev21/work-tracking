<?php

namespace App\Filament\Resources\InternTypeResource\Pages;

use App\Filament\Resources\InternTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Http\Controllers\SyncController;

class ManageInternTypes extends ManageRecords
{
    protected static string $resource = InternTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sincronizar')
                ->action(function () {
                    $controller = new SyncController();
                    $controller->syncInternTypes();
                    $this->notify('success', 'Tipos de pasantes sincronizados correctamente');
                })
                ->color('primary')
                ->icon('heroicon-o-arrow-path'),
        ];
    }
}
