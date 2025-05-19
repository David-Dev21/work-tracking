<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
    public static function form(Form $form): Form
    {
        $user = Filament::auth()->user();
        $area_id = null;

        if ($responsible = $user->responsible) {
            $area_id = $responsible->areaRole->area_id;
        } elseif ($intern = $user->interns) {
            $latestRegistration = $intern->internRegistrations()
                ->latest('start_date')
                ->first();
            if ($latestRegistration) {
                $area_id = $latestRegistration->area_id;
            }
        }
        return $form
            ->schema([
                Forms\Components\Tabs::make('Project')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('headings.Project'))
                            ->icon('heroicon-o-folder')
                            ->schema([
                                Forms\Components\Split::make([
                                    Forms\Components\Section::make([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('headings.Title'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->label(__('headings.Description'))
                                            ->autosize()
                                    ]),
                                    Forms\Components\Section::make([
                                        Forms\Components\Select::make('area_id')
                                            ->relationship('area', 'name')
                                            ->required()
                                            ->default($area_id),
                                        Forms\Components\Radio::make('state')
                                            ->label(__('headings.State'))
                                            ->inline()
                                            ->inlineLabel(false)
                                            ->options([
                                                'pendiente' => __('headings.Pending'),
                                                'en_progreso' => __('headings.In Progress'),
                                                'finalizado' => __('headings.Finished'),
                                            ])
                                            ->hiddenOn('create')
                                            ->default('pendiente')
                                            ->required(),
                                        Forms\Components\TextInput::make('advance')
                                            ->label(__('headings.Advance'))
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%')
                                            ->numeric()
                                            ->hiddenOn('create')
                                            ->default(0),
                                        Forms\Components\DateTimePicker::make('start_date')
                                            ->label(__('headings.Start Date'))
                                            ->default(now())
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->displayFormat('d/m/Y')
                                            ->required(),
                                        Forms\Components\DateTimePicker::make('end_date')
                                            ->label(__('headings.End Date'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->displayFormat('d/m/Y'),
                                    ]),
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('headings.Activities'))->icon('heroicon-c-clipboard-document-list')
                            ->schema([
                                Repeater::make('Activities')
                                    ->relationship('activities')
                                    ->hiddenLabel()
                                    ->schema([
                                        Forms\Components\Split::make([
                                            Forms\Components\Section::make([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('headings.Title'))
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('description')
                                                    ->label(__('headings.Description'))
                                                    ->autosize()
                                                    ->columnSpanFull(),
                                            ]),
                                            Forms\Components\Section::make([
                                                Forms\Components\Select::make('state')
                                                    ->label(__('headings.State'))
                                                    ->hiddenOn('create')
                                                    ->options([
                                                        'pendiente' => __('headings.Pending'),
                                                        'en_progreso' => __('headings.In Progress'),
                                                        'finalizado' => __('headings.Finished'),
                                                    ])
                                                    ->placeholder(false)
                                                    ->default('pendiente')
                                                    ->native(false)
                                                    ->required()
                                                    ->columnSpan(4),
                                                Forms\Components\Select::make('priority')
                                                    ->label(__('headings.Priority'))
                                                    ->options([
                                                        'baja' => __('headings.Low'),
                                                        'media' => __('headings.Medium'),
                                                        'alta' => __('headings.High'),
                                                    ])
                                                    ->placeholder(false)
                                                    ->native(false)
                                                    ->default('media')
                                                    ->required()
                                                    ->columnSpan(4),
                                                Forms\Components\DateTimePicker::make('start_date')
                                                    ->label(__('headings.Start Date'))
                                                    ->default(now())
                                                    ->native(false)
                                                    ->suffixIcon('heroicon-o-calendar')
                                                    ->displayFormat('d/m/Y')
                                                    ->columnSpan(4)
                                                    ->required(),
                                                Forms\Components\DateTimePicker::make('end_date')
                                                    ->label(__('headings.End Date'))
                                                    ->native(false)
                                                    ->suffixIcon('heroicon-o-calendar')
                                                    ->displayFormat('d/m/Y')
                                                    ->columnSpan(4),
                                                Forms\Components\Hidden::make('area_id')
                                                    ->default($area_id),
                                            ]),
                                        ])->columnSpanFull(),
                                    ])->collapsible()
                                    ->reorderable(),
                            ])->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state'),
                Tables\Columns\TextColumn::make('advance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
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
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
