<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('ログイン画面を表示できる', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('ユーザーはログイン画面から認証できる', function () {
    $user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_auth@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => false,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect();
});

test('無効なパスワードでは認証されない', function () {
    $user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_auth_fail@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => false,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('ユーザーはログアウトできる', function () {
    $user = User::create([
        'name' => 'テスト公家',
        'email' => 'nobleman_logout@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => false,
    ]);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});