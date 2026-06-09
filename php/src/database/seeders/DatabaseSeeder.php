<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\ShopType;
use App\Models\ShoppingItem;
use App\Models\PurchaseLog;
use App\Models\CurrentCart;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. 管理者
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

        // 過去の履歴（1日前）
        PurchaseLog::create([
            'shopping_item_id' => $item1->id,
            'price' => 250,
            'quantity'     => '1パック',
            'shop_type' => ShopType::Supermarket,
            'purchased_at' => now()->subDays(1), 
        ]);

        // ★ 新設：管理者の「たまご」が、今回の買い物リスト（カート）に入っている状態を作る
        CurrentCart::create([
            'shopping_item_id' => $item1->id,
            'price'            => 250,       // 編集可能な今回の予定価格
            'quantity'         => 1,         // 編集可能な今回の予定数量（数値）
            'shop_type'        => ShopType::Supermarket,
        ]);


        // --- 一般ユーザーのデータ（牛乳） ---
        $item2 = ShoppingItem::create([
            'user_id' => $user->id,
            'name' => '牛乳',
        ]);

        PurchaseLog::create([
            'shopping_item_id' => $item2->id,
            'price' => 200,
            'shop_type' => ShopType::Dollarsgore,
            'purchased_at' => now()->subDays(5),
        ]);
    }
}
