<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns a successful response', function () {
    $user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_example@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => false,
    ]);

    // 認証済み状態（actingAs）でトップページへアクセス
    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('shopping.index'));
});