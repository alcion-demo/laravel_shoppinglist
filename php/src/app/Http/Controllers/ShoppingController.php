<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\ShopType;
use App\Models\User;
use App\Models\PurchaseLog;
use App\Models\ShoppingItem;
use App\Services\ShoppingService;
use App\Http\Requests\StoreShoppingRequest;
use App\Http\Requests\UpdateShoppingRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\CurrentCart;

class ShoppingController extends Controller
{
    /**
     * __construct
     */
    public function __construct(
        protected ShoppingService $shoppingservice,
        protected CurrentCart $currentCart,
    ){}

    public function index()
    {
        $userId = auth()->id();

        return view('shopping.index', [
            'items'         => CurrentCart::forUser($userId)->with('item')->get(),
            'history'       => PurchaseLog::forUser($userId)->with('item')->latest('purchased_at')->get()->groupBy('purchased_date_string'),
            'frequentItems' => PurchaseLog::getFrequentItems($userId), 
        ]);
    }

    /**
     * 編集画面を表示する
     */
    public function edit(int $shopping)
    {
        // 編集したいカートの商品を取得
        $cart = CurrentCart::with('item')->findOrFail($shopping);
        
        // edit.blade.php へデータを渡して表示
        return view('shopping.edit', compact('cart'));
    }

    public function store(StoreShoppingRequest $request)
    {
        $validated = $request->validated();
        $this->shoppingservice->savePurchase(auth()->id(),$validated);
        return redirect()->route('shopping.index');
    }

    /**
     * 編集された内容で買い物リスト（カート）を更新する
     */
    public function update(UpdateShoppingRequest $request, int $shopping)
    {
        $cart = CurrentCart::findOrFail($shopping);

        // フォームから送られてきた内容でカートを更新
        $cart->update([
            'price'     => $request->input('price', 0),
            'quantity'  => $request->input('quantity'),
            'shop_type' => $request->input('shop_type'),
        ]);

        // もし商品名（ShoppingItemの名前）も変更できるようにしたい場合は以下も追記
        $cart->item->update([
            'name' => $request->input('name')
        ]);

        return redirect()->route('shopping.index')->with('message', 'リストを修正しました！');
    }

    public function destroy(int $shopping) {
        //論理削除
        $cart = CurrentCart::findOrFail($shopping);
        $cart->delete();
        return redirect()->back();
    }

    /**
     * カートのアイテムを購入完了（履歴へ移動）にする
     *
     * @param integer $id
     * @return void
     */
    public function purchase(int $shopping)
    {

        $this->shoppingservice->recordPurchase($shopping, []);

        return redirect()->back()->with('message', '購入記録を保存しました！');
    }
}
