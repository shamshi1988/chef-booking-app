<?php

use App\Http\Controllers\ServiceController;
use App\Livewire\Professionals;
use Illuminate\Support\Facades\Route;

Route::get('/', [ServiceController::class, 'home'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/book', [ServiceController::class, 'book'])->name('book');
Route::get('/professionals', Professionals::class)->name('professionals');

Route::view('/become-a-partner', 'become-partner')->name('become-partner');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms-conditions', 'terms-conditions')->name('terms-conditions');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'hi', 'bn'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Old chef-only URLs keep working.
Route::get('/submit-request', fn () => redirect()->route('book', request()->query(), 301));
Route::get('/our-chefs', fn () => redirect()->route('professionals', ['service' => 'private-chef'], 301));
Route::get('/become-partner-chef', fn () => redirect()->route('become-partner', [], 301));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
