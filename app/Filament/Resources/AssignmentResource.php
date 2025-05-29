<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Models\Activity;
use App\Models\Assignment;
use App\Models\Intern;
use App\Models\InternRegistration;
use App\Models\Project;
use App\Traits\HasAreaScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-c-arrows-right-left';

    public static function getNavigationGroup(): string
    {
        return __('headings.Work Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('headings.Assignments');
    }
    public static function getPluralLabel(): string
    {
        return __('headings.Assignments');
    }
    public static function getLabel(): string
    {
        return __('headings.Assignment');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make([
                    Forms\Components\Section::make('')
                        ->schema([
                            Forms\Components\Select::make('intern_id')
                                ->label(__('headings.Intern'))
                                ->searchable()
                                ->preload()
                                ->multiple()
                                ->options(function () {
                                    $areaId = HasAreaScope::getUserAreaId();
                                    if ($areaId) {
                                        // Get interns in the current user's area
                                        $internIds = InternRegistration::where('area_id', $areaId)
                                            ->pluck('intern_id')
                                            ->toArray();
                                        return Intern::whereIn('id', $internIds)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    }
                                    return Intern::pluck('name', 'id')->toArray();
                                })
                                ->dehydrated()
                                ->required()
                                ->validationMessages(
                                    [
                                        'required' => __('headings.Required Field'),
                                    ]
                                ),
                            Forms\Components\DatePicker::make('assigned_date')
                                ->label(__('headings.Assigned Date'))
                                ->native(false)
                                ->suffixIcon('heroicon-o-calendar')
                                ->default(now())
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                        ])->columns(1),
                    Forms\Components\Section::make('')
                        ->schema([
                            Forms\Components\Select::make('project_id')
                                ->label(__('headings.Project'))
                                ->options(function () {
                                    $areaId = HasAreaScope::getUserAreaId();
                                    if ($areaId) {
                                        // Filter projects by area
                                        return Project::where('area_id', $areaId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    }
                                    return Project::pluck('name', 'id')->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->requiredWithout('activity_id')
                                ->validationMessages([
                                    'required_without' => __('headings.Required Field Without Activity'),
                                ]),
                            Forms\Components\Select::make('activity_id')
                                ->label(__('headings.Activity'))
                                ->options(function () {
                                    $areaId = HasAreaScope::getUserAreaId();
                                    if ($areaId) {
                                        // Filter activities by area
                                        return Activity::where('area_id', $areaId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    }
                                    return Activity::pluck('name', 'id')->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->requiredWithout('project_id')
                                ->validationMessages([
                                    'required_without' => __('headings.Required Field Without Project'),
                                ]),
                        ])->columns(1),
                ])
                    ->from('md')
                    ->columnSpanFull(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('intern.name')
                    ->label(__('headings.Intern'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('headings.Project'))
                    ->searchable()
                    ->visible(fn($livewire) => $livewire->activeTab === 'projects'),
                Tables\Columns\TextColumn::make('activity.name')
                    ->label(__('headings.Activity'))
                    ->searchable()
                    ->visible(fn($livewire) => $livewire->activeTab === 'activities'),
                Tables\Columns\TextColumn::make('assigned_date')
                    ->label(__('headings.Assigned Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'view' => Pages\ViewAssignment::route('/{record}'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
