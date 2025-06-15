<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use App\Models\Location;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;


class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-c-clipboard-document-list';

    public static function getNavigationGroup(): string
    {
        return __('headings.Work Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('headings.Activities');
    }
    public static function getPluralLabel(): string
    {
        return __('headings.Activities');
    }
    public static function getLabel(): string
    {
        return __('headings.Activity');
    }

    public static function getNavigationBadge(): ?string
    {
        // Obtener el usuario autenticado
        $user = Filament::auth()->user();

        if ($user) {
            // Verificar si el usuario es un pasante (intern)
            $intern = \App\Models\Intern::where('user_id', $user->id)->first();

            // Si es un pasante, contar solo sus actividades asignadas y las de sus proyectos
            if ($intern) {
                // Obtener IDs de actividades asignadas directamente al pasante
                $assignedActivityIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->pluck('activity_id')
                    ->toArray();

                // Obtener IDs de proyectos asignados al pasante
                $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->unique()
                    ->toArray();

                // Contar actividades que:
                // 1. Están asignadas directamente al pasante, O
                // 2. Pertenecen a proyectos asignados al pasante
                return static::getModel()::where(function ($query) use ($assignedActivityIds, $assignedProjectIds) {
                    $query->whereIn('id', $assignedActivityIds)
                        ->orWhereIn('project_id', $assignedProjectIds);
                })->count();
            }

            // Para responsables, contar actividades según el área
            $area_id = static::getModel()::getUserAreaId();
            if ($area_id) {
                return static::getModel()::where('area_id', $area_id)->count();
            }
        }

        // Para otros usuarios, contar todas las actividades
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        $area_id = static::$model::getUserAreaId();
        return $form
            ->schema([
                Forms\Components\Tabs::make('Activity')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('headings.Activity'))
                            ->icon('heroicon-c-clipboard-document-list')
                            ->schema([
                                Forms\Components\Split::make([
                                    Forms\Components\Section::make([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('headings.Title'))
                                            ->required()
                                            ->validationMessages(
                                                ['required' => __('headings.Required Field')]
                                            )
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->label(__('headings.Description'))
                                            ->rows(5)
                                            ->autosize(),
                                    ]),
                                    Forms\Components\Section::make([
                                        Forms\Components\Select::make('area_id')
                                            ->label(__('headings.Area'))
                                            ->options(function () use ($area_id) {
                                                if ($area_id) {
                                                    // If area is assigned, keep it fixed
                                                    return \App\Models\Area::where('id', $area_id)
                                                        ->pluck('name', 'id')
                                                        ->toArray();
                                                }
                                                // If no area is assigned, show all areas
                                                return \App\Models\Area::pluck('name', 'id')->toArray();
                                            })
                                            ->disabled(fn() => $area_id !== null)
                                            ->default($area_id)
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull()
                                            ->dehydrated()
                                            ->required()
                                            ->validationMessages(
                                                ['required' => __('headings.Required Field')]
                                            ),
                                        Forms\Components\Select::make('state')
                                            ->label(__('headings.State'))
                                            ->options([
                                                'pendiente' => '<span style="background-color: #EF4444; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.Pending') . '</span>',
                                                'en_progreso' => '<span style="background-color: #F59E0B; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.In Progress') . '</span>',
                                                'finalizado' => '<span style="background-color: #10B981; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.Finished') . '</span>',
                                            ])
                                            ->allowHtml()
                                            ->placeholder(false)
                                            ->native(false)
                                            ->default('pendiente')
                                            ->hiddenOn('create')
                                            ->required()
                                            ->validationMessages(
                                                ['required' => __('headings.Required Field')]
                                            ),
                                        Forms\Components\Select::make('priority')
                                            ->label(__('headings.Priority'))
                                            ->options([
                                                'baja' => '<span style="background-color: #64cbde; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.Low') . '</span>',
                                                'media' => '<span style="background-color: #F59E0B; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.Medium') . '</span>',
                                                'alta' => '<span style="background-color: #EF4444; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.High') . '</span>',
                                            ])
                                            ->allowHtml()
                                            ->placeholder(false)
                                            ->native(false)
                                            ->default('media')
                                            ->required()
                                            ->validationMessages(
                                                ['required' => __('headings.Required Field')]
                                            ),
                                        Forms\Components\Select::make('project_id')
                                            ->label(__('headings.Project'))
                                            ->searchable()
                                            ->options(function () use ($area_id) {
                                                // Obtener el usuario autenticado
                                                $user = Filament::auth()->user();

                                                if (!$user) {
                                                    return [];
                                                }

                                                // Verificar si el usuario es un pasante (intern)
                                                $intern = \App\Models\Intern::where('user_id', $user->id)->first();

                                                // Verificar si el usuario es un responsable (responsible)
                                                $responsible = \App\Models\Responsible::where('user_id', $user->id)->first();

                                                // Si es un pasante (intern), mostrar solo los proyectos asignados a él
                                                if ($intern) {
                                                    // Obtener los IDs de proyectos asignados al intern
                                                    $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                                                        ->whereNotNull('project_id')
                                                        ->pluck('project_id')
                                                        ->unique()
                                                        ->toArray();

                                                    $query = \App\Models\Project::whereIn('id', $assignedProjectIds);

                                                    // Aplicar filtro de área si es necesario
                                                    if ($area_id) {
                                                        $query->where('area_id', $area_id);
                                                    }

                                                    return $query->pluck('name', 'id')->toArray();
                                                }

                                                // Si es un responsable (responsible), mostrar solo los proyectos de su área
                                                if ($responsible) {
                                                    // Obtener el área del responsable a través de su rol de área
                                                    $areaRole = $responsible->areaRole;

                                                    if ($areaRole) {
                                                        $responsibleAreaId = $areaRole->area_id;

                                                        // Mostrar proyectos del área del responsable
                                                        return \App\Models\Project::where('area_id', $responsibleAreaId)
                                                            ->pluck('name', 'id')
                                                            ->toArray();
                                                    }
                                                }

                                                // Para otros usuarios o cuando no hay asignaciones específicas
                                                if ($area_id) {
                                                    // Filter projects by area
                                                    return \App\Models\Project::where('area_id', $area_id)
                                                        ->pluck('name', 'id')
                                                        ->toArray();
                                                }
                                                // If no area is assigned, show all projects
                                                return \App\Models\Project::pluck('name', 'id')->toArray();
                                            })
                                            ->preload()
                                            ->native(false)
                                            ->columnSpanFull()
                                            ->default(null),
                                    ])->columns(2)
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('headings.Location'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Section::make('Location')
                                    ->schema([
                                        Map::make('location')
                                            ->defaultLocation([-0.1807, -78.4678]) // Quito coordinates
                                            ->autocompleteReverse()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if (isset($state['location'])) {
                                                    $location = Location::create([
                                                        'address' => $state['location']['address'] ?? '',
                                                        'latitude' => $state['location']['lat'] ?? null,
                                                        'longitude' => $state['location']['lng'] ?? null,
                                                    ]);
                                                    $set('location_id', $location->id);
                                                }
                                            }),
                                        Forms\Components\Hidden::make('location_id'),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('area.name')
                    ->label(__('headings.Area'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('headings.Project'))
                    ->searchable()
                    ->visible(fn($livewire) => $livewire->activeTab !== 'without_project'),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('headings.Activity'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('headings.State'))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pendiente' => 'danger',
                        'en_progreso' => 'warning',
                        'finalizado' => 'success',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('headings.Priority'))
                    ->badge()
                    ->color(fn($record) => match ($record->priority) {
                        'baja' => 'primary',
                        'media' => 'warning',
                        'alta' => 'danger',
                    }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'view' => Pages\ViewActivity::route('/{record}'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Obtener el usuario autenticado
        $user = Filament::auth()->user();

        if ($user) {
            // Verificar si el usuario es un pasante (intern)
            $intern = \App\Models\Intern::where('user_id', $user->id)->first();

            // Si es un pasante, mostrar sus actividades asignadas y las de sus proyectos
            if ($intern) {
                // Obtener IDs de actividades asignadas directamente al pasante
                $assignedActivityIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->pluck('activity_id')
                    ->toArray();

                // Obtener IDs de proyectos asignados al pasante
                $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->unique()
                    ->toArray();

                // Crear una consulta que incluya:
                // 1. Actividades asignadas directamente al pasante
                // 2. Actividades que pertenecen a proyectos asignados al pasante
                return $query->where(function ($query) use ($assignedActivityIds, $assignedProjectIds) {
                    $query->whereIn('id', $assignedActivityIds)
                        ->orWhereIn('project_id', $assignedProjectIds);
                });
            }

            // Para responsables y otros usuarios, aplicar el filtro por área (si corresponde)
            return static::$model::scopeFilterByUserArea($query);
        }

        return static::$model::scopeFilterByUserArea($query);
    }
}
