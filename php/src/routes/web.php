<?php
declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShoppingController;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return redirect()->route('shopping.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // ショッピングリスト（メイン画面）
    Route::post('/shopping/{id}/purchase', [ShoppingController::class, 'purchase'])->name('shopping.purchase');
    Route::post('/shopping/suggest', [ShoppingController::class, 'suggest'])->name('shopping.suggest');

    // 標準のCRUD処理をリソースでひとまとめにする（必要なものだけ only で指定）
    Route::resource('shopping', ShoppingController::class)->only(['index', 'store', 'destroy', 'edit', 'update']);

    Route::get('/shopping/recipe-status/{jobId}', [ShoppingController::class, 'getRecipeStatus']);

    // 管理者のみ：ユーザー登録（Breezeのデフォルトを上書きorラップ）
    // ゲート 'admin-only' は AppServiceProvider で定義済みである必要があります
    Route::middleware(['can:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
