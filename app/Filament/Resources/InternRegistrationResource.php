<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternRegistrationResource\Pages;
use App\Filament\Resources\InternRegistrationResource\RelationManagers;
use App\Models\InternRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternRegistrationResource extends Resource
{
    protected static ?string $model = InternRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-s-identification';

    public static function getNavigationGroup(): string
    {
        return __('headings.Staff Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('headings.Intern Registrations');
    }
    public static function getPluralLabel(): string
    {
        return __('headings.Intern Registrations');
    }
    public static function getLabel(): string
    {
        return __('headings.Intern Registration');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('internType.name')
                    ->label(__('headings.Intern Type'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('intern.name')
                    ->label(__('headings.Intern'))
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->label(__('headings.Area'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('headings.Start Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('headings.End Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('headings.First Sync'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('headings.Last Sync'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInternRegistrations::route('/'),
        ];
    }
}
