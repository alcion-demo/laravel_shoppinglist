<?php
declare(strict_types=1);

use App\Models\User;
use App\Models\ShoppingItem;
use App\Models\CurrentCart;
use App\Services\ShoppingService;
use App\Enums\ShopType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // テスト用ユーザーの作成
    $this->user = User::create([
        'name' => 'テストユーザー',
        'email' => 'testuser@example.com',
        'password' => bcrypt('password123'),
    ]);

    // ShoppingServiceをモック化してバインド
    $this->shoppingServiceMock = Mockery::mock(ShoppingService::class);
    $this->app->instance(ShoppingService::class, $this->shoppingServiceMock);
});

test('認証済みユーザーが GET /shopping で買い物リストページを表示できる', function () {
    $response = $this->actingAs($this->user)
        ->get('/shopping');

    $response->assertStatus(200)
        ->assertViewIs('shopping.index')
        ->assertViewHas(['items', 'history', 'frequentItems']);
});

test('認証済みユーザーが POST /shopping で買い物アイテムを追加できる', function () {
    // コントローラー内部で呼び出される savePurchase メソッドの挙動を定義
    $this->shoppingServiceMock->shouldReceive('savePurchase')
        ->once()
        ->with(Mockery::any(), Mockery::any());

    $postData = [
        'name' => 'にんじん',
        'price' => 150,
        'quantity' => '3本', // StoreShoppingRequestの正規表現「^[0-9]+.*」に準拠
    ];

    $response = $this->actingAs($this->user)
        ->post('/shopping', $postData);

    $response->assertRedirect();
});

test('認証済みユーザーが PATCH /shopping/{id} でカートアイテムを更新できる', function () {
    $item = ShoppingItem::create([
        'user_id' => $this->user->id,
        'name' => 'たまねぎ',
    ]);

    $cart = CurrentCart::create([
        'shopping_item_id' => $item->id,
        'price' => 100,
        'quantity' => '1個',
        'shop_type' => ShopType::Supermarket,
    ]);

    $updateData = [
        'name' => 'たまねぎ（大）',
        'price' => 120,
        'quantity' => '2個',
        'shop_type' => ShopType::Supermarket->value,
    ];

    $response = $this->actingAs($this->user)
        ->patch("/shopping/{$cart->id}", $updateData);

    $response->assertRedirect();
});

test('認証済みユーザーが DELETE /shopping/{id} でカートアイテムを削除できる', function () {
    $item = ShoppingItem::create([
        'user_id' => $this->user->id,
        'name' => 'キャベツ',
    ]);

    $cart = CurrentCart::create([
        'shopping_item_id' => $item->id,
        'price' => 200,
        'quantity' => '1玉',
        'shop_type' => ShopType::Supermarket,
    ]);

    $response = $this->actingAs($this->user)
        ->delete("/shopping/{$cart->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('current_carts', [
        'id' => $cart->id,
    ]);
});

test('認証済みユーザーが POST /shopping/{id}/purchase で購入完了できる', function () {
    // コントローラー内部で呼び出される recordPurchase メソッドの挙動を定義
    $this->shoppingServiceMock->shouldReceive('recordPurchase')
        ->once()
        ->with(Mockery::any(), Mockery::any());

    $item = ShoppingItem::create([
        'user_id' => $this->user->id,
        'name' => '牛乳',
    ]);

    $cart = CurrentCart::create([
        'shopping_item_id' => $item->id,
        'price' => 250,
        'quantity' => '1本',
        'shop_type' => ShopType::Supermarket,
    ]);

    $response = $this->actingAs($this->user)
        ->post("/shopping/{$cart->id}/purchase");

    $response->assertRedirect();
});