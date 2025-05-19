<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SyncButton extends Widget
{
    protected static string $view = 'filament.widgets.sync-button';
    protected int | string | array $columnSpan = 'full';
}
