<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;

Route::middleware(['auth'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    
    // Placeholder routes for now
    Route::get('/books', \App\Livewire\Books\Index::class)->name('books.index');
    Route::get('/books/create', \App\Livewire\Books\BookForm::class)->name('books.create');
    Route::get('/books/{id}/edit', \App\Livewire\Books\BookForm::class)->name('books.edit');
    Route::get('/projects', \App\Livewire\Projects\Index::class)->name('projects.index');
    Route::get('/projects/create', \App\Livewire\Projects\ProjectForm::class)->name('projects.create');
    Route::get('/projects/{id}/edit', \App\Livewire\Projects\ProjectForm::class)->name('projects.edit');
    Route::get('/borrowings', \App\Livewire\Borrowings\Index::class)->name('borrowings.index');
    Route::get('/staff', \App\Livewire\Staff\Index::class)->name('staff.index');
    Route::get('/staff/permissions', \App\Livewire\Admin\StaffPermissions::class)->name('staff.permissions');
    Route::get('/account', \App\Livewire\Account::class)->name('account');
    
    Route::post('/logout', function() {
        auth()->logout();
        return redirect()->route('login');
    })->name('logout');
});

Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
