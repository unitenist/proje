<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminContentController;
use App\Http\Controllers\AdminMessagesController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Middleware\EnsureAdmin;
use App\Support\SiteContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home', [
    'site' => SiteContent::site(),
    'assistant' => SiteContent::assistant(),
    'content' => SiteContent::home(),
    'about' => SiteContent::about(),
    'approach' => SiteContent::approach(),
    'services' => SiteContent::services(),
    'skills' => SiteContent::homeSkills(),
    'lab' => SiteContent::lab(),
    'testimonials' => SiteContent::homeTestimonials(),
    'contact' => SiteContent::contact(),
]))->name('home');

Route::post('/iletisim', [ContactMessageController::class, 'store'])->name('contact.store');
Route::post('/robot-lead', [ContactMessageController::class, 'storeRobotLead'])->name('robot-lead.store');

// Shared hosting providers may disable symlink() and exec(), preventing
// `storage:link`. Apache forwards this missing public path to Laravel.
Route::get('/media/{path}', function (string $path) {
    abort_if(str_contains($path, '..') || ! Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('media.show');

// Legacy multi-page URLs now redirect to the matching anchor on the single-page site.
Route::redirect('/hizmetler', '/#services');
Route::redirect('/yaklasimimiz', '/#resume');
Route::redirect('/ai-lab', '/#portfolio');
Route::redirect('/hakkimizda', '/#about');
Route::redirect('/iletisim', '/#contact')->name('contact.redirect');

Route::get('/sitemap.xml', function () {
    return response()
        ->view('sitemap')
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::get('/', [AdminContentController::class, 'dashboard'])->name('dashboard');
        Route::post('/upload', [AdminContentController::class, 'upload'])->name('upload');
        Route::get('/messages', [AdminMessagesController::class, 'index'])->name('messages.index');
        Route::post('/messages/{message}/read', [AdminMessagesController::class, 'markRead'])->name('messages.read');
        Route::delete('/messages/{message}', [AdminMessagesController::class, 'destroy'])->name('messages.destroy');
        Route::get('/{section}', [AdminContentController::class, 'edit'])->name('content.edit');
        Route::put('/{section}', [AdminContentController::class, 'update'])->name('content.update');
    });
});

