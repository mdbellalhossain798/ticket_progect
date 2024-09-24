<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\HomeController; // Make sure this is included
use Illuminate\Support\Facades\Auth; // Correctly include the Auth facade

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [TicketController::class, 'index'])->name('admin.dashboard');
    Route::get('/customer/dashboard', [TicketController::class, 'customerDashboard'])->name('customer.dashboard');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('customer/save-ticket',[TicketController::class,'saveTicket'])->name('customer.save-ticket');
});

