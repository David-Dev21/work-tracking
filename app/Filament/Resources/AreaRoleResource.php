<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaRoleResource\Pages;
use App\Filament\Resources\AreaRoleResource\RelationManagers;
use App\Models\AreaRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AreaRoleResource extends Resource
{
    protected static ?string $model = AreaRole::class;

    protected static ?string $navigationIcon = 'heroicon-m-briefcase';

    public static function getNavigationGroup(): string
    {
        return __('headings.Area Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('headings.Area Roles');
    }
    public static function getPluralLabel(): string
    {
        return __('headings.Area Roles');
    }
    public static function getLabel(): string
    {
        return __('headings.Area Role');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('headings.Area Role'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->label(__('headings.Area'))
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
            'index' => Pages\ManageAreaRoles::route('/'),
        ];
    }
}
