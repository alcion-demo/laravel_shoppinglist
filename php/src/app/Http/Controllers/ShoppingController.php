<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\ShopType;
use App\Models\User;
use App\Models\PurchaseLog;
use App\Models\ShoppingItem;
use App\Services\ShoppingService;
use App\Http\Requests\StoreSoppingRequest;
use Illuminate\Support\Facades\Auth;


class ShoppingController extends Controller
{
    /**
     * __construct
     */
    public function __construct(
        protected ShoppingService $shoppingservice,
    ){}

    public function index()
    {
        $userId = auth()->id();

        return view('shopping.index', [
            // 無名関数を使わず、定義済みのリレーション名を指定
            'items' => ShoppingItem::forUser($userId)
                        ->active()
                        ->with('recentPurchaseLogs')
                        ->get(),

            // アクセサ名 'purchased_date_string' を指定してグループ化
            'history' => PurchaseLog::whereHas('item', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        })
                        ->with('item')
                        ->latest('purchased_at')
                        ->get()
                        ->groupBy('purchased_date_string'),
        ]);
    }

    public function store(StoreSoppingRequest $request)
    {
        $validated = $request->validated();
        $this->shoppingservice->savePurchase(auth()->id(),$validated);
        return redirect()->route('shopping.index');
    }

    public function destroy(ShoppingItem $shoppingItem) {
        //論理削除
        $shoppingItem->update(['is_active' => false]);
        return redirect()->back();
    }
}
