<?php
declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\ShoppingItem;
use App\Models\CurrentCart;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| 未認証（ゲスト）ユーザーのアクセス制御テスト
|--------------------------------------------------------------------------
*/

test('未認証ユーザーが GET /shopping にアクセスするとログイン画面へリダイレクトされる', function () {
    $this->get('/shopping')
        ->assertRedirect('/login');
});

test('未認証ユーザーが GET /shopping/{id}/edit にアクセスするとログイン画面へリダイレクトされる', function () {
    $this->get('/shopping/1/edit')
        ->assertRedirect('/login');
});

test('未認証ユーザーが GET /profile にアクセスするとログイン画面へリダイレクトされる', function () {
    $this->get('/profile')
        ->assertRedirect('/login');
});

test('未認証ユーザーが GET /admin/users にアクセスするとログイン画面へリダイレクトされる', function () {
    $this->get('/admin/users')
        ->assertRedirect('/login');
});

/*
|--------------------------------------------------------------------------
| 一般ユーザー（認証済・非管理者）のアクセス制御テスト
|--------------------------------------------------------------------------
*/

test('一般ユーザーが GET /shopping にアクセスできる', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/shopping')
        ->assertStatus(200);
});

test('一般ユーザーが GET /shopping/{id}/edit にアクセスできる', function () {
    $user = User::factory()->create(['is_admin' => false]);

    // 1. 商品マスターデータを最小限で作成
    $item = ShoppingItem::create([
        'user_id' => $user->id,
        'name'    => 'テスト商品',
    ]);

    // 2. コントローラーの CurrentCart::findOrFail() を通過させるため、カートデータを生成
    $cart = CurrentCart::create([
        'shopping_item_id' => $item->id,
        'price'            => 150,
        'quantity'         => '1個',
    ]);

    // カートのIDを使ってアクセスをシミュレート
    $this->actingAs($user)
        ->get("/shopping/{$cart->id}/edit")
        ->assertStatus(200);
});

test('一般ユーザーが GET /profile にアクセスできる', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/profile')
        ->assertStatus(200);
});

test('一般ユーザーが GET /admin/users にアクセスすると権限拒否（403）される', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/admin/users')
        ->assertStatus(403);
});