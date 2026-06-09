<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ShoppingItem;
use App\Models\PurchaseLog;
use Illuminate\Support\Facades\Http;
use App\Enums\ShopType;
use App\Models\CurrentCart;

class ShoppingService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function savePurchase(int $userId, array $data): CurrentCart
    {

        $item = ShoppingItem::where('user_id', $userId)
                ->where('name', $data['name'])
                ->first();

        if (!$item) {
            $item = ShoppingItem::create([
                'user_id' => $userId,
                'name'    => $data['name'],
                'is_active' => true, 
            ]);
        }

        // 2. すでに今回のカート（CurrentCart）に同じ商品が入っているか確認
        $cartItem = CurrentCart::where('shopping_item_id', $item->id)->first();

        if ($cartItem) {
            // 【編集対応】フォームから送られてきた新しい数量・価格・店舗情報で「上書き」します
            $cartItem->update([
                'quantity'  => $data['quantity'] ?? $cartItem->quantity,
                'price'     => isset($data['price']) ? (int)$data['price'] : $cartItem->price,
                'shop_type' => $data['shop_type'] ?? $cartItem->shop_type->value,
            ]);
            return $cartItem;
        }

        // 3. カートに新しく登録する
        return CurrentCart::create([
            'shopping_item_id' => $item->id,
            'price'            => isset($data['price']) ? (int)$data['price'] : 0,
            'quantity'         => $data['quantity'] ?? null,
            'shop_type'        => $data['shop_type'] ?? ShopType::Supermarket->value,
        ]);
    }

    /**
     * 購入完了ボタンを押した時に、カートから履歴へ書き写す処理
     *
     * @param integer $itemId
     * @param array $data
     * @return void
     */
    public function recordPurchase(int $cartId, array $data): PurchaseLog
    {
        $cart = CurrentCart::findOrFail($cartId);

        // 1. 購入履歴（PurchaseLog）の作成
        $log = PurchaseLog::create([
            'shopping_item_id' => $cart->shopping_item_id,
            'price'            => isset($data['price']) ? (int)$data['price'] : $cart->price,
            'quantity'         => $data['quantity'] ?? $cart->quantity, // 文字列のまま履歴へ引き継ぎ
            'shop_type'        => $data['shop_type'] ?? $cart->shop_type->value,
            'purchased_at'     => now(),
        ]);

        // 2. 購入が完了したので、今回のカートからは削除する
        $cart->delete();

        return $log;
    }

    /**
     * Gemini API連携: 献立提案
     */
    public function getRecipeSuggestions(array $items)
    {
        $apiKey = config('services.gemini.key');
        $prompt = implode(',', $items) . " を使った簡単な献立を3つ提案して。";

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        return $response->json('candidates.0.content.parts.0.text');
    }
}
