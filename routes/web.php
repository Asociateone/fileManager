<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use App\Models\File;
use Illuminate\Support\Facades\Route;

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

Route::get('/dashboard', function () {
    $files = File::whereUserId(Auth::user()->id)->get();

    return view('dashboard', compact('files'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
	Route::resource('profile', ProfileController::class)->except(['index', 'create']);
//	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/upload-bestanden', [UploadController::class, 'page'])->name('upload.page');
    Route::post('/upload-bestanden', [UploadController::class, 'uploaden'])->name('upload.insert');
    Route::get('/upload/{file_id}/download/{password}', [UploadController::class, 'downloaden'])->name('upload.download');
    // Route::get("/upload-download/{file_id}/{password}", [UploadController::class, "downloaden"])->name("upload.download");
    Route::post('/upload-password', [UploadController::class, 'guessPassword'])->name('upload.enterPassword');
});

require __DIR__.'/auth.php';
