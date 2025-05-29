<?php

namespace App\Filament\Resources\AssignmentResource\Pages;

use App\Filament\Resources\AssignmentResource;
use App\Models\Assignment;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAssignments extends ListRecords
{
    protected static string $resource = AssignmentResource::class;

    public ?string $activeTab = 'projects';

    public function getTabs(): array
    {
        return [
            'projects' => Tab::make(__('headings.Projects'))
                ->badge(Assignment::query()->whereNotNull('project_id')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('project_id')),
            'activities' => Tab::make(__('headings.Activities'))
                ->badge(Assignment::query()->whereNotNull('activity_id')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('activity_id')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('headings.Assign Projects or Activities'))
                ->icon('heroicon-o-plus')
                ->size('xl')
        ];
    }
}
