<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    $this->getLoginFormComponent(), // Ini bakal manggil method di bawah
                    $this->getPasswordFormComponent(),
                    $this->getRememberFormComponent(),
                ])
                ->statePath('data'),
        ];
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('username') // Ganti name jadi username
            ->label('Username') // Nah, labelnya udah jadi Username nih!
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'], // Kasih tau Laravel buat nyari kolom username
            'password' => $data['password'],
        ];
    }
}