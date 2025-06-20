<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponsibleResource\Pages;
use App\Models\Responsible;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class ResponsibleResource extends Resource
{
    protected static ?string $model = Responsible::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): string
    {
        return __('headings.Staff Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('headings.Responsibles');
    }
    public static function getPluralLabel(): string
    {
        return __('headings.Responsibles');
    }
    public static function getLabel(): string
    {
        return __('headings.Responsible');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('areaRole.name')
                    ->label(__('headings.Area Role'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_degree')
                    ->label(__('headings.Academic Degree'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('headings.Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(__('headings.Last Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('identity_card')
                    ->label(__('headings.Identity Card'))
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
            ])
            ->actions([
                Action::make('createUser')
                    ->label(__('headings.Create User'))
                    ->visible(fn(Responsible $record): bool => Filament::auth()->check() && !$record->user_id)
                    ->color('primary')
                    ->icon('heroicon-o-user-plus')
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel(__('headings.Create User'))
                    ->action(function (Responsible $record): void {
                        if (!$record->user_id) {
                            $username = $record->identity_card;
                            $name = $record->name . ' ' . $record->last_name;

                            // Check for soft deleted user with same username
                            $existingUser = User::withTrashed()
                                ->where('username', $username)
                                ->first();

                            if ($existingUser) {
                                // Restore existing user
                                $existingUser->restore();
                                $record->user()->associate($existingUser);
                                $record->save();

                                Notification::make()
                                    ->title('User restored successfully')
                                    ->success()
                                    ->send();
                            } else {
                                // Create new user if no soft deleted user exists
                                $password = $record->identity_card . '_' . strtolower(explode(' ', $record->name)[0]);
                                $user = User::create([
                                    'name' => $name,
                                    'username' => $username,
                                    'password' => Hash::make($password),
                                ]);

                                $user->assignRole('responsable');
                                $record->user()->associate($user);
                                $record->save();

                                Notification::make()
                                    ->title('User created successfully')
                                    ->success()
                                    ->send();
                            }
                        }
                    }),

                Action::make('deleteUser')
                    ->label(__('headings.Delete User'))
                    ->visible(fn(Responsible $record): bool => Filament::auth()->check() && $record->user_id)
                    ->color('danger')
                    ->icon('heroicon-o-user-minus')
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel(__('headings.Delete User'))
                    ->action(function (Responsible $record): void {
                        if ($record->user_id) {
                            $user = $record->user;
                            $record->user()->dissociate();
                            $record->save();
                            $user->delete();
                            Notification::make()
                                ->title('User deleted successfully')
                                ->success()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageResponsibles::route('/'),
        ];
    }
}
