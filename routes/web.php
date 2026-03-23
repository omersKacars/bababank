<?php

use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\ChildMessageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ParentChildController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ParentSocialController;
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
    Route::patch('/children/{child}/password', [ParentChildController::class, 'updatePassword'])->name('parent.children.password.update');
    Route::delete('/children/{child}', [ParentChildController::class, 'destroy'])->name('parent.children.destroy');
    Route::post('/children/{child}/transactions', [TransactionController::class, 'store'])->name('parent.transactions.store');
    Route::patch('/transactions/{transaction}/void', [TransactionController::class, 'void'])->name('parent.transactions.void');

    Route::get('/social', [ParentSocialController::class, 'index'])->name('parent.social.index');
    Route::post('/social/requests/{receiver}', [ParentSocialController::class, 'sendRequest'])->name('parent.social.request.send');
    Route::patch('/social/requests/{friendRequest}', [ParentSocialController::class, 'respondRequest'])->name('parent.social.request.respond');
});

Route::middleware(['auth', 'role:child'])->prefix('child')->group(function () {
    Route::get('/dashboard', [ChildDashboardController::class, 'index'])->name('child.dashboard');
    Route::post('/messages', [ChildMessageController::class, 'store'])->name('child.messages.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'storeMessage'])->name('conversations.messages.store');
});

require __DIR__.'/auth.php';
