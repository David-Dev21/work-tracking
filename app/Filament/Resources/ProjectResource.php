<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard;
use Filament\Facades\Filament;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-s-folder';

    public static function getNavigationGroup(): string
    {
        return __('headings.Work Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('headings.Projects');
    }
    public static function getPluralLabel(): string
    {
        return __('headings.Projects');
    }
    public static function getLabel(): string
    {
        return __('headings.Project');
    }

    public static function getNavigationBadge(): ?string
    {
        // Obtener el usuario autenticado
        $user = Filament::auth()->user();

        if ($user) {
            // Verificar si el usuario es un pasante (intern)
            $intern = \App\Models\Intern::where('user_id', $user->id)->first();

            // Si es un pasante, contar solo sus proyectos asignados
            if ($intern) {
                // Obtener IDs de proyectos asignados al pasante
                $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->unique()
                    ->toArray();

                // Contar proyectos asignados al pasante
                return static::getModel()::whereIn('id', $assignedProjectIds)->count();
            }

            // Para responsables, contar proyectos según el área
            $area_id = static::getModel()::getUserAreaId();
            if ($area_id) {
                return static::getModel()::where('area_id', $area_id)->count();
            }
        }

        // Para otros usuarios, contar todos los proyectos
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        $area_id = static::$model::getUserAreaId();
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('project')
                        ->label(__('headings.Project'))
                        ->icon('heroicon-o-folder')
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
                                    Forms\Components\DatePicker::make('start_date')
                                        ->label(__('headings.Start Date'))
                                        ->default(now())
                                        ->native(false)
                                        ->suffixIcon('heroicon-o-calendar')
                                        ->displayFormat('d F Y')
                                        ->required()
                                        ->validationMessages(
                                            ['required' => __('headings.Required Field')]
                                        ),
                                    Forms\Components\DatePicker::make('end_date')
                                        ->label(__('headings.End Date'))
                                        ->native(false)
                                        ->suffixIcon('heroicon-o-calendar')
                                        ->displayFormat('d F Y'),
                                ])->columns(2)
                            ]),
                        ]),
                    Wizard\Step::make('activities')
                        ->label(__('headings.Activities'))
                        ->icon('heroicon-c-clipboard-document-list')
                        ->schema([
                            TableRepeater::make('Activities')
                                ->label(__('headings.Activities'))
                                ->relationship('activities')
                                ->required(false)
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record) {
                                    // Asegurar que se hereda el área del proyecto
                                    $data['area_id'] = $record->area_id;
                                    return $data;
                                })
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label(__('headings.Title'))
                                        ->maxLength(255)
                                        ->disabledOn('edit'),
                                    Forms\Components\TextInput::make('description')
                                        ->label(__('headings.Description'))
                                        ->maxLength(255)
                                        ->disabledOn('edit'),
                                    Forms\Components\Select::make('state')
                                        ->label(__('headings.State'))
                                        ->options([
                                            'pendiente' => '<span style="background-color: #EF4444; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.Pending') . '</span>',
                                            'en_progreso' => '<span style="background-color: #F59E0B; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.In Progress') . '</span>',
                                            'finalizado' => '<span style="background-color: #10B981; color: white; padding: 2px 8px; border-radius: 5px;">' . __('headings.Finished') . '</span>',
                                        ])
                                        ->allowHtml()
                                        ->placeholder(false)
                                        ->default('pendiente')
                                        ->disabledOn('edit')
                                        ->native(false),
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
                                        ->disabledOn('edit')
                                        ->default('media'),
                                    Forms\Components\Hidden::make('area_id')
                                        ->default($area_id)
                                        ->dehydrated(),
                                ])
                                ->columnSpan('full')
                                ->defaultItems(0),
                        ]),
                ])
                    ->columnSpanFull()
                    ->persistStepInQueryString()
                    ->skippable('activities'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('area.name')
                    ->label(__('headings.Area')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('headings.Project'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('headings.State'))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pendiente' => 'danger',
                        'en_progreso' => 'warning',
                        'finalizado' => 'success',
                    }),
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label(__('headings.Advance'))
                    ->numeric()
                    ->formatStateUsing(fn(string $state): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString("
                        <div style=\"position:relative; min-width:60px; max-width:100px;\">
                            <div style=\"width:100%; background-color:#71717a; height:22px; border-radius:5px; overflow:hidden; position:relative;\">
                                <div style=\"position:absolute; background-color:#64cbde; height:100%; border-radius:5px; width:{$state}%;\"></div>
                                <div style=\"position:absolute; width:100%; height:100%; display:flex; align-items:center; justify-content:center;\">
                                    <strong style=\"color:white; z-index:1;\">{$state}%</strong>
                                </div>
                            </div>
                        </div>
                    ")),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('headings.Start Date'))
                    ->since()
                    ->dateTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('headings.End Date'))
                    ->since()
                    ->dateTooltip()
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->withCount(['activities', 'activities as completed_activities_count' => function ($query) {
                $query->where('state', 'finalizado');
            }])
            ->with(['assignments.intern']);

        // Obtener el usuario autenticado
        $user = Filament::auth()->user();

        if ($user) {
            // Verificar si el usuario es un pasante (intern)
            $intern = \App\Models\Intern::where('user_id', $user->id)->first();

            // Si es un pasante, mostrar solo sus proyectos asignados
            if ($intern) {
                // Obtener IDs de proyectos asignados al pasante
                $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->unique()
                    ->toArray();

                // Filtrar la consulta para mostrar solo esos proyectos
                $query->whereIn('id', $assignedProjectIds);

                // No aplicar el filtro por área para pasantes, ya que solo deben ver sus proyectos asignados
                return $query;
            }

            // Para responsables y otros usuarios, aplicar el filtro por área (si corresponde)
            return static::$model::scopeFilterByUserArea($query);
        }

        return static::$model::scopeFilterByUserArea($query);
    }
}
