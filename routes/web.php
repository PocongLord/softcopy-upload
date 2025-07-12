<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Exports\InvoicesExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;


Route::get('/', function () {
    return redirect()->route('login');
});


// Auto Redirect ke Halaman Sesuai Role
Route::get('/dashboard', function () {
    if (!Auth::user()) {
        return redirect()->route('login');
    }
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.invoices');
    }
    return redirect()->route('invoices.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route untuk semua user yang sudah login (Admin & User Biasa)
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User (role: user) Invoice Routes
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/export/excel', function () {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    })->name('invoices.export');
});

// Admin Only Routes
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/invoices', [InvoiceController::class, 'adminIndex'])->name('admin.invoices');
    Route::get('/admin/invoices/create', [InvoiceController::class, 'adminCreate'])->name('admin.invoices.create');
    Route::post('/admin/invoices/store', [InvoiceController::class, 'adminStore'])->name('admin.invoices.store');
    Route::get('/admin/invoices/download/{invoice}', [InvoiceController::class, 'download'])->name('admin.invoices.download');
    Route::get('/admin/invoices/export', [InvoiceController::class, 'export'])->name('admin.invoices.export');
    Route::post('/admin/invoices/{id}/done', [InvoiceController::class, 'markAsDone'])->name('admin.invoices.done');
    Route::delete('/admin/invoices/{id}', [InvoiceController::class, 'destroy'])->name('admin.invoices.destroy');
    Route::get('/admin/invoices/fetch', [InvoiceController::class, 'fetchInvoicesAdmin'])->name('admin.invoices.fetch');
});


Route::middleware('auth')->group(function () {
    // Route user untuk melihat & download file invoice
    Route::get('/invoices/download/{invoice}', [InvoiceController::class, 'userDownload'])->name('invoices.download');
    // Route untuk fetch data invoice
    Route::get('/api/invoices', [InvoiceController::class, 'fetchInvoices'])->name('invoices.fetch');
});


require __DIR__.'/auth.php';


