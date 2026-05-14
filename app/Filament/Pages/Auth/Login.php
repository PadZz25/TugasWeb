<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification; // Tambahin ini
use Illuminate\Validation\ValidationException; // Tambahin ini

class Login extends BaseLogin
{
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    $this->getLoginFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getRememberFormComponent(),
                ])
                ->statePath('data'),
        ];
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    // --- INI PEMBENAHANNYA: SIHIR NOTIFIKASI GAGAL ---
    protected function throwFailureValidationException(): never
    {
        Notification::make()
            ->title('Login Gagal!')
            ->body('Username atau password anda salah, coba cek lagi ya.')
            ->danger() // Warna merah
            ->persistent() // Biar nggak ilang sendiri sebelum di-close
            ->send();

        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}