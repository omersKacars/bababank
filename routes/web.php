<?php

use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\ParentChildController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['tr', 'en'], true), 404);
    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    return $user->isParent()
        ? redirect()->route('parent.dashboard')
        : redirect()->route('child.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:parent'])->prefix('parent')->group(function () {
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('parent.dashboard');
    Route::post('/children', [ParentChildController::class, 'store'])->name('parent.children.store');
    Route::post('/children/{child}/transactions', [TransactionController::class, 'store'])->name('parent.transactions.store');
});

Route::middleware(['auth', 'role:child'])->prefix('child')->group(function () {
    Route::get('/dashboard', [ChildDashboardController::class, 'index'])->name('child.dashboard');
});

require __DIR__.'/auth.php';
