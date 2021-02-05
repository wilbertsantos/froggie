<?php


use App\Http\Controllers\Frog\FrogCrawlerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [FrogCrawlerController::class, 'index'])
	->name('frog');

Route::get('/frog', [FrogCrawlerController::class, 'index'])
	->name('frog');

Route::post('/frog', [FrogCrawlerController::class, 'crawl'])
    ->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');





require __DIR__.'/auth.php';
