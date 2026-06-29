<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('メール確認画面を表示できる', function () {
    $user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_verify_view@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => null,
        'is_admin' => false,
    ]);

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
});

test('メールアドレスを確認できる', function () {
    $user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_verify@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => null,
        'is_admin' => false,
    ]);

    // Verified イベントのみをフェイクし、他のシステム依存リスナーの実行を妨げない（500エラーを回避）
    Event::fake([
        Verified::class,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    
    // 既存仕様のリダイレクト動作を検証
    $response->assertRedirect();
});

test('不正なハッシュではメール確認されない', function () {
    $user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_verify_bad@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => null,
        'is_admin' => false,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});