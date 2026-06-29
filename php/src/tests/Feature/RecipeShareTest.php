<?php
declare(strict_types=1);

use App\Models\User;
use App\Services\RecipeShareService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // テスト用ユーザーの作成
    $this->user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_share@example.com',
        'password' => bcrypt('password123'),
    ]);
});

test('有効な共有データで GET /shopping/share/{data} が表示できる', function () {
    // 1. 既存の RecipeShareService を用いて、正しい共有用エンコードデータを生成
    $recipeService = new RecipeShareService();
    $mockRecipe = [
        'name' => '雅な京風肉じゃが',
        'ingredients' => ['じゃがいも', 'にんじん'],
        'amount' => '2人前'
    ];
    
    // 生成されるURL（例: http://localhost/shopping/share/{encodedData}）から、パラメータ部分のみを抽出
    $shareUrl = $recipeService->encode($mockRecipe);
    $urlParts = explode('/shopping/share/', $shareUrl);
    $validData = end($urlParts);

    // 2. 生成した有効なパラメータでGETリクエストを送信
    $response = $this->actingAs($this->user)
        ->get("/shopping/share/{$validData}");

    // 正常に表示される（200 OK）こと、およびビューに必要な情報が含まれているかを検証
    $response->assertStatus(200);
    $response->assertSee('雅な京風肉じゃが');
});

test('無効な共有データで GET /shopping/share/{data} が 404 になる', function () {
    // RecipeShareService::decode() が「あな、おそろしや無効なデータでおじゃる」と投げるような、
    // base64デコード不可能、あるいは解凍不可能な不正データ
    $invalidData = 'invalid-corrupted-base64-data-that-fails-decoding';

    $response = $this->actingAs($this->user)
        ->get("/shopping/share/{$invalidData}");

    // サービス層の InvalidArgumentException 等を受け、コントローラー側で適切に404処理される既存仕様を検証
    $response->assertStatus(404);
});