<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternTypeResource\Pages;
use App\Filament\Resources\InternTypeResource\RelationManagers;
use App\Models\InternType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternTypeResource extends Resource
{
    protected static ?string $model = InternType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): string
    {
        return __('headings.Staff Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('headings.Intern Types');
    }
    public static function getPluralLabel(): string
    {
        return __('headings.Intern Types');
    }
    public static function getLabel(): string
    {
        return __('headings.Intern Type');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('headings.Intern Type'))
                    ->searchable(),
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
            'index' => Pages\ManageInternTypes::route('/'),
        ];
    }
}
