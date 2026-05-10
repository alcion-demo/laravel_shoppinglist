<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ShoppingItem;
use App\Models\PurchaseLog;
use Illuminate\Support\Facades\Http;
use App\Enums\ShopType;


class ShoppingService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function savePurchase(int $userId, array $data): PurchaseLog
    {
        $item = ShoppingItem::where('user_id', $userId)
                ->where('name', $data['name'])
                ->first();

        if ($item) {
        $item->update(['is_active' => true]);
        } else {
            $item = ShoppingItem::create([
                'user_id' => $userId,
                'name'    => $data['name'],
                'is_active' => true,
            ]);
        }

        // 2. 購入履歴の作成
        return PurchaseLog::create([
            'shopping_item_id' => $item->id,
            'price'            => $data['price'] ?? 0,
            'quantity'         => $data['quantity'] ?? null,
            'shop_type'        => $data['shop_type'] ?? ShopType::Supermarket->value,
            'purchased_at'     => now(),
        ]);
    }

    public function recordPurchase(int $itemId, array $data)
    {
        return PurchaseLog::create([
            'shopping_item_id' => $itemId,
            'quantity'     => $data['quantity'] ?? null,
            'price'        => $data['price'] ?? null,
            'shop_type' => $data['shop_type'],
            'purchased_at' => now(),
        ]);
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
