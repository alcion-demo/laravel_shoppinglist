<?php
declare(strict_types=1);

use App\Models\User;
use App\Jobs\GenerateRecipeJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // テスト用ユーザーの作成
    $this->user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman@example.com',
        'password' => bcrypt('password123'),
    ]);
});

test('認証済みユーザーが POST /shopping/suggest で献立提案をリクエストできる', function () {
    Bus::fake();

    $postData = [
        'ingredients' => "にんじん\nたまねぎ\nじゃがいも",
    ];

    $response = $this->actingAs($this->user)
        ->post('/shopping/suggest', $postData);

    // 既存仕様のリダイレクト（302）を検証
    $response->assertRedirect();
    Bus::assertDispatched(GenerateRecipeJob::class);
});

test('無効な食材入力時に shopping.index?tab=recipe へリダイレクトし、suggestion エラーが表示される', function () {
    // 同じ文字が3回連続する無効な入力データ
    $invalidData = [
        'ingredients' => '怪しい文字あああ',
    ];

    $response = $this->actingAs($this->user)
        ->from('/shopping')
        ->post('/shopping/suggest', $invalidData);

    // StoreSuggestionRequest で定義されたリダイレクト先とエラーバッグの検証
    $response->assertRedirect('/shopping?tab=recipe');
    $response->assertSessionHasErrors(['ingredients'], null, 'suggestion');
});

test('GET /shopping/recipe-status/{jobId} で pending JSON を返す', function () {
    $jobId = 'test-job-pending-123';
    
    // キャッシュがまだ存在しない、または未完了の状態
    Cache::forget("recipe_{$jobId}");

    $response = $this->actingAs($this->user)
        ->get("/shopping/recipe-status/{$jobId}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'pending',
        ]);
});

test('GET /shopping/recipe-status/{jobId} で completed JSON を返す', function () {
    $jobId = 'test-job-completed-123';
    
    // GenerateRecipeJob の実装に準拠したキャッシュデータのセット
    Cache::put("recipe_{$jobId}", [
        'status' => 'completed',
        'recipes' => [
            [
                'name' => '雅な肉じゃが',
                'ingredients' => ['にんじん', 'じゃがいも'],
                'amount' => '2人前',
            ]
        ]
    ], now()->addMinutes(10));

    $response = $this->actingAs($this->user)
        ->get("/shopping/recipe-status/{$jobId}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'completed',
            'recipes' => [
                ['name' => '雅な肉じゃが']
            ],
        ]);
});

test('GET /shopping/recipe-status/{jobId} で error JSON を返す', function () {
    $jobId = 'test-job-error-123';
    
    // GenerateRecipeJob 内の例外キャッチ時の構造に準拠したセット
    Cache::put("recipe_{$jobId}", [
        'status' => 'error',
        'message' => '食材として認識できなかったのじゃ'
    ], now()->addMinutes(10));

    $response = $this->actingAs($this->user)
        ->get("/shopping/recipe-status/{$jobId}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'error',
            'message' => '食材として認識できなかったのじゃ',
        ]);
});