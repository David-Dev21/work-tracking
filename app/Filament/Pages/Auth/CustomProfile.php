<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('headings.Name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('username')
                    ->label(__('headings.Username'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(__('headings.New Password'))
                    ->password()
                    ->rule(Password::default())
                    ->autocomplete('new-password')
                    ->dehydrated(fn($state): bool => filled($state))
                    ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                    ->live(debounce: 500)
                    ->same('passwordConfirmation'),
                TextInput::make('passwordConfirmation')
                    ->label(__('headings.Confirm New Password'))
                    ->password()
                    ->required()
                    ->visible(fn($get): bool => filled($get('password')))
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    protected function getRedirectUrl(): ?string
    {
        return route('filament.admin.pages.dashboard');
    }
}
