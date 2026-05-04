<?php

use App\Livewire\Auth\AdminLogin;
use App\Livewire\Dashboard\AdminsPage;
use App\Livewire\Dashboard\CompaniesPage;
use App\Livewire\Dashboard\CustomersPage;
use App\Livewire\Dashboard\StudentsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard/login', AdminLogin::class)->name('dashboard.login');

Route::middleware(['auth', 'admin'])->group(function (): void {
	Route::get('/dashboard', fn () => redirect()->route('dashboard.companies'))->name('dashboard.home');
	Route::get('/dashboard/companies', CompaniesPage::class)->name('dashboard.companies');
	Route::get('/dashboard/customers', CustomersPage::class)->name('dashboard.customers');
	Route::get('/dashboard/admins', AdminsPage::class)->name('dashboard.admins');
	Route::get('/dashboard/students', StudentsPage::class)->name('dashboard.students');

	Route::post('/dashboard/logout', function (Request $request) {
		Auth::guard('web')->logout();

		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect()->route('dashboard.login');
	})->name('dashboard.logout');
});
