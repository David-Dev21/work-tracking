<?php

namespace App\Filament\Resources\AreaRoleResource\Pages;

use App\Filament\Resources\AreaRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Http\Controllers\SyncController;

class ManageAreaRoles extends ManageRecords
{
    protected static string $resource = AreaRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sincronizar')
                ->action(function () {
                    $controller = new SyncController();
                    $controller->syncAreaRoles();
                    $this->notify('success', 'Cargos sincronizados correctamente');
                })
                ->color('primary')
                ->icon('heroicon-o-arrow-path'),
        ];
    }
}
