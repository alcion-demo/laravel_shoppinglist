<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\ShopType;
use App\Models\ShoppingItem;
use App\Models\PurchaseLog;
use App\Models\CurrentCart;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ユーザー作成
        $admin = User::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'is_admin' => true]);
        $user = User::create(['name' => '一般ユーザー', 'email' => 'user@example.com', 'password' => bcrypt('password'), 'is_admin' => false]);

        // テスト用の商品リスト
        $items = ['たまご', '牛乳', '食パン', 'バナナ', '納豆', 'ヨーグルト'];

        // 各ユーザーごとにデータを生成
        foreach ([$admin, $user] as $owner) {
            foreach ($items as $name) {
                $item = ShoppingItem::create([
                    'user_id' => $owner->id,
                    'name' => $name,
                ]);

                // ランダムに購入履歴を生成（「よく買うもの」をテストするため、回数にバラつきを出す）
                $buyCount = rand(1, 10); 
                for ($i = 0; $i < $buyCount; $i++) {
                    PurchaseLog::create([
                        'shopping_item_id' => $item->id,
                        'price' => rand(100, 500),
                        'quantity' => '1個',
                        'shop_type' => ShopType::Supermarket,
                        'purchased_at' => now()->subDays(rand(1, 30)),
                    ]);
                }
            }

            // カートにもいくつか入れておく
            CurrentCart::create([
                'shopping_item_id' => ShoppingItem::where('user_id', $owner->id)->first()->id,
                'price' => 200,
                'quantity' => 1,
                'shop_type' => ShopType::Supermarket,
            ]);
        }
    }
}