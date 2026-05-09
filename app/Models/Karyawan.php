<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable implements FilamentUser
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $timestamps = false;
    protected $guarded = [];

    // Kasih tau Laravel kalau identitas utamanya adalah username
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    // Kasih tau Laravel nama kolom password yang kita pakai
    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    // Kasih tau Laravel dari mana dia harus ngambil nilai passwordnya
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Syarat akses panel Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->nama; 
    }

    // Bikin properti 'name' bayangan biar Filament gak rewel
    public function getNameAttribute()
    {
        return $this->nama;
    }
}