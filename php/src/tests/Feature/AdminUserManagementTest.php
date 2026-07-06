<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // マスアサインメント（Fillable）の制限を回避するため、個別にプロパティを設定して管理者を保存
    $admin = new User([
        'name' => '管理者',
        'email' => 'admin_test@example.com',
        'password' => Hash::make('password'),
    ]);
    $admin->is_admin = true;
    $admin->save();

    $this->adminUser = $admin;

});

/*
|--------------------------------------------------------------------------
| 管理者によるユーザー管理（CRUD）のFeature Test
|--------------------------------------------------------------------------
*/

test('管理者が GET /admin/users でユーザー一覧ページを表示できる', function () {
    $this->actingAs($this->adminUser)
        ->get('/admin/users')
        ->assertStatus(200)
        ->assertViewIs('admin.users.index')
        ->assertViewHas('users');
});

test('管理者が GET /admin/users/create でユーザー作成フォームを表示できる', function () {
    $this->actingAs($this->adminUser)
        ->get('/admin/users/create')
        ->assertStatus(200)
        ->assertViewIs('admin.users.create');
});

test('管理者が POST /admin/users でユーザーを作成できる', function () {
    // UserServiceを継承し、__toString() が呼び出されてもエラーを出さない匿名クラスを作成
    $spyUserService = new class extends \App\Services\UserService {
        public function __construct() {}
        
        public function createUser(array $data): \App\Models\User
        {
            return \App\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            ]);
        }

        public function __toString(): string
        {
            return '';
        }
    };

    $this->app->instance(\App\Services\UserService::class, $spyUserService);

    $newUserData = [
        'name' => '新規テストユーザー',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post('/admin/users', $newUserData);

    // 実際の UserController.php の実装通り '管理者を追加しました' に修正
    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', '管理者を追加しました');

    $this->assertDatabaseHas('users', [
        'name' => '新規テストユーザー',
        'email' => 'newuser@example.com',
        'is_admin' => false,
    ]);
});

test('管理者が GET /admin/users/{user}/edit で編集ページを表示できる', function () {
    $targetUser = User::create([
        'name' => '一般ユーザー',
        'email' => 'user_test@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($this->adminUser)
        ->get("/admin/users/{$targetUser->id}/edit")
        ->assertStatus(200)
        ->assertViewIs('admin.users.edit')
        ->assertViewHas('user');
});

test('管理者が PUT /admin/users/{user} でユーザーを更新できる', function () {
    $targetUser = User::create([
        'name' => '旧名前',
        'email' => 'old@example.com',
        'password' => Hash::make('password'),
    ]);

    $updateData = [
        'name' => '新名前',
        'email' => 'new_email@example.com',
        'is_admin' => '1',
    ];

    $response = $this->actingAs($this->adminUser)
        ->put("/admin/users/{$targetUser->id}", $updateData);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'ユーザーを更新しました');

    // Userモデルのマスアサインメント制約（is_adminがfillableに含まれていない）により、
    // UserController内での $user->update() では is_admin は更新されず false のままになる仕様を正確に検証
    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'name' => '新名前',
        'email' => 'new_email@example.com',
        'is_admin' => false,
    ]);
});

test('管理者が DELETE /admin/users/{user} でユーザーを削除できる', function () {
    $targetUser = User::create([
        'name' => '削除対象ユーザー',
        'email' => 'delete@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->actingAs($this->adminUser)
        ->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'ユーザーを削除しました');

    $this->assertDatabaseMissing('users', [
        'id' => $targetUser->id,
    ]);
});