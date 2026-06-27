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
use App\Jobs\GenerateRecipeJob;
use Illuminate\Support\Facades\Cache;
use App\Services\RecipeShareService;

class ShoppingController extends Controller
{
    /**
     * __construct
     */
    public function __construct(
        protected ShoppingService $shoppingservice,
        protected CurrentCart $currentCart,
        protected RecipeShareService $shareService,
    ){}

    public function index()
    {
        $userId = auth()->id();
        \Log::info('recipe job session', [
            'job_id' => session('job_id'),
            'latest_recipes' => session('latest_recipes'),
        ]);

        return view('shopping.index', [
            'items'         => CurrentCart::forUser($userId)->with('item')->get(),
            'history'       => PurchaseLog::forUser($userId)->with('item')->latest('purchased_at')->get()->groupBy('purchased_date_string'),
            'frequentItems' => PurchaseLog::getFrequentItems($userId),
            // セッションからデータがあれば渡す（なければ空配列）
            'recipes'       => session('latest_recipes', []),
            'job_id'        => session('job_id'),
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

    public function destroy(int $shopping)
    {
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
        $ingredients = $request->validated()['ingredients'];
        $items = explode("\n", str_replace("\r", "", $ingredients));

        foreach ($items as $item) {
            $item = trim($item);

            if ($this->shoppingservice->isInvalid($item)) {
                return back()->withErrors([
                    'ingredients' => '食材でないものを入力とな？'
                ], 'suggestion');
            }
        }

        // 件数を計算
        $count = $this->decideRecipeCount($items);
        $jobId = uniqid('recipe_');

        GenerateRecipeJob::dispatch($jobId, $ingredients, auth()->id(), $count);

        session()->put('job_id', $jobId);

        return redirect()->route('shopping.index', ['tab' => 'recipe'])
            ->withInput();
    }

    public function share(string $data, RecipeShareService $shareService)
    {
        try {
            $recipe = $shareService->decode($data);
            return view('shopping.share', ['recipe' => $recipe]);
        } catch (\Exception $e) {
            abort(404, '献立の復元に失敗いたしました');
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

    /**
     * レシピ取得
     *
     * @param string $jobId
     * @return void
     */
    public function getRecipeStatus(string $jobId)
    {
        $data = Cache::get("recipe_{$jobId}");

        // AI処理が終わっていなければ status: pending を返す
        if (!$data) {
            return response()->json(['status' => 'pending']);
        }

        // エラー情報が含まれている場合はそれを返す
        if ($data['status'] === 'error') {
            session()->forget('job_id');
            session()->save();

            return response()->json([
                'status' => 'error',
                'message' => $data['message']
            ]);
        }

        // ここで共有URLを各レシピに注入する
        $recipes = array_map(function ($recipe) {
            $recipe['share_url'] = $this->shareService->encode($recipe);
            return $recipe;
        }, $data['recipes']);

        //表示一時保存
        session()->put('latest_recipes', $recipes);
        session()->forget('job_id'); // 完了したら不要

        session()->save(); // 確実化

        // AI処理が終わっていればレシピデータを返す
        return response()->json([
            'status' => 'completed',
            'recipes' => $recipes
        ]);
    }
}
