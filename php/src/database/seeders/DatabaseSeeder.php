<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\ShopType;
use App\Models\ShoppingItem;
use App\Models\PurchaseLog;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // 2. 一般ユーザー (比較用)
        $user = User::create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // --- 管理者のデータ（たまご） ---
        $item1 = ShoppingItem::create([
            'user_id' => $admin->id,
            'name' => 'たまご',
        ]);

        PurchaseLog::create([
            'shopping_item_id' => $item1->id,
            'price' => 250,
            'quantity'     => '1パック',
            'shop_type' => ShopType::Supermarket,
            'purchased_at' => now()->subDays(1), // 昨日の購入（3日以内表示のテスト）
        ]);

        // --- 一般ユーザーのデータ（牛乳） ---
        // これが管理者の画面に出てこなければ「出し分け成功」
        $item2 = ShoppingItem::create([
            'user_id' => $user->id,
            'name' => '牛乳',
        ]);

        PurchaseLog::create([
            'shopping_item_id' => $item2->id,
            'price' => 200,
            'shop_type' => ShopType::Dollarsgore,
            'purchased_at' => now()->subDays(5), // 5日前の購入
        ]);
    }
}
