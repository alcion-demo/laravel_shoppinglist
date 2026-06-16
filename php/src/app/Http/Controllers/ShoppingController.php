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
use App\Ai\Agents\NoblemanAgent;
use App\Http\Requests\StoreSuggestionRequest;
use Laravel\Ai\Responses\StructuredTextResponse;

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
            // セッションからデータがあれば渡す（なければ空配列）
            'recipes'       => session('recipes', []),
            'ai_error'      => session('ai_error'),
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
        $this->shoppingservice->savePurchase(auth()->id(), $validated);
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

    /**
     * AI献立提案
     *
     * @param Request $request
     * @param ShoppingService $service
     * @return void
     */
    public function suggest(StoreSuggestionRequest $request, NoblemanAgent $agent)
    {
        $userId = auth()->id();
        $validated = $request->validated();
        $items_input = explode("\n", str_replace("\r", "", $validated['ingredients']));
        $count = $this->decideRecipeCount($items_input);

        try {
            $start = microtime(true);
            $response = $agent->prompt("冷蔵庫に" . implode('、', $items_input) . "があるのじゃ。"
                        . "{$count}個の献立を提案せよ。");
// logger()->info('recipe sec', [
//     'time' => round(microtime(true) - $start, 2)
// ]);
            // AI が構造化出力を返す場合は `structured` を直接取得する
            if ($response instanceof StructuredTextResponse) {
                $data = $response->structured;
            } else {
                $data = is_array($response) ? $response : json_decode(json_encode($response), true);
            }

            // 成功時のみリダイレクト（パラメータを付与してタブを保持）
            return redirect()->route('shopping.index', ['tab' => 'recipe'])
                ->with('recipes', $data['recipes'] ?? []);

        } catch (\Exception $e) {
            $msg = $e->getMessage();

            if (str_contains($msg, 'rate limit')) {
                return back()->with('ai_error', '混み合っておる（無料枠制限）');
            }

            if (str_contains($msg, 'timeout')) {
                return back()->with('ai_error', '思考に時間がかかりすぎたのじゃ…もう一度試してたもれ');
            }

            if (str_contains($msg, 'schema')) {
                return back()->with('ai_error', '献立の形が崩れてしまったようじゃ');
            }

            return back()->with('ai_error', 'これまた珍妙なエラーが起きたのじゃ');

        }
    }

    /**
     * レシピ数決定
     *
     * @param array $items
     * @return integer
     */
        private function decideRecipeCount(array $items): int
    {
        $count = count($items);

        if ($count <= 2) {
            return 1;
        }

        if ($count <= 5) {
            return 3;
        }

        return 5;
    }
}
