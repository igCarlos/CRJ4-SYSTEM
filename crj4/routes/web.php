<?php

use App\Http\Controllers\facturaController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('filament.dashboard.pages.dashboard');
});



Route::middleware(['auth'])->group(function () {
    Route::get('sales/{sale}/factura', [facturaController::class, 'show'])->name('sales.factura.show');
    Route::get('sales/{sale}/factura/pdf', [facturaController::class, 'pdf'])->name('sales.factura.pdf');
});
// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
