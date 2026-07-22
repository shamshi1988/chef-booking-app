<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/become-partner-chef', 'become-partner-chef')->name('become-partner-chef');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms-conditions', 'terms-conditions')->name('terms-conditions');
Route::get('/our-chefs', \App\Livewire\OurChefs::class)->name('our-chefs');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'hi'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/submit-request', function () {
    return view('submit-request');
})->name('submit-request');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
