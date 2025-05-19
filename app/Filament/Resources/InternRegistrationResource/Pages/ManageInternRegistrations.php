<?php

namespace App\Filament\Resources\InternRegistrationResource\Pages;

use App\Filament\Resources\InternRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Http\Controllers\SyncController;

class ManageInternRegistrations extends ManageRecords
{
    protected static string $resource = InternRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sincronizar')
                ->action(function () {
                    $controller = new SyncController();
                    $controller->syncInternRegistrations();
                    $this->notify('success', 'Registros de pasantes sincronizados correctamente');
                })
                ->color('primary')
                ->icon('heroicon-o-arrow-path'),
        ];
    }
}
