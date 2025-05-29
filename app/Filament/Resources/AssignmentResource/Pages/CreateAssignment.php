<?php

namespace App\Filament\Resources\AssignmentResource\Pages;

use App\Filament\Resources\AssignmentResource;
use App\Models\Assignment;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssignment extends CreateRecord
{
    protected static string $resource = AssignmentResource::class;

    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->submit(null)
            ->label(__('headings.Assign Projects or Activities'))
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeValidate(array $data): array
    {
        // If project_id is empty, set it to null
        if (isset($data['project_id']) && empty($data['project_id'])) {
            $data['project_id'] = null;
        }

        // If activity_id is empty, set it to null
        if (isset($data['activity_id']) && empty($data['activity_id'])) {
            $data['activity_id'] = null;
        }

        return $data;
    }

    /**
     * Handle the creation of multiple assignments for multiple interns
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Get selected interns
        $internIds = $data['intern_id'] ?? [];

        // Remove intern_id from data as we'll handle it separately
        unset($data['intern_id']);

        // Add default role value
        $data['role'] = 'Assignee';

        $createdAssignments = collect();

        // Create an assignment for each intern
        foreach ($internIds as $internId) {
            $assignmentData = array_merge($data, ['intern_id' => $internId]);
            $assignment = Assignment::create($assignmentData);
            $createdAssignments->push($assignment);
        }

        // Return the first assignment (required by the parent method)
        return $createdAssignments->first() ?? new Assignment();
    }
}
