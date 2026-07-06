<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Pharmacies;
use App\Livewire\Settings\PracticeSettings;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\RolesAndPermissions;
use App\Livewire\Settings\Security;
use App\Livewire\Settings\SmartPhrases;
use App\Livewire\Settings\UserManagement;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('profile.edit');
});

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::livewire('settings/smart-phrases', SmartPhrases::class)->name('smart-phrases.edit');

    Route::livewire('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::livewire('settings/users', UserManagement::class)->name('settings.users');

    Route::livewire('settings/pharmacies', Pharmacies::class)->name('settings.pharmacies');

    Route::livewire('settings/roles', RolesAndPermissions::class)->name('settings.roles');

    Route::livewire('settings/practice', PracticeSettings::class)->name('settings.practice');

    Route::livewire('settings/security', Security::class)
        ->middleware([
            'password.confirm',
        ])
        ->name('security.edit');
});

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');
