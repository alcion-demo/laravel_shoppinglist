<?php
declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use App\Services\RecipeGenerator;

class GenerateRecipeJob implements ShouldQueue
{
    use Queueable;

    protected $jobId;
    protected $ingredients;
    protected $userId;
    protected $recipeCount;

    /**
     * Create a new job instance.
     */
    public function __construct($jobId, $ingredients, $userId, $recipeCount)
    {
        $this->jobId = $jobId;
        $this->ingredients = $ingredients;
        $this->userId = $userId;
        $this->recipeCount = $recipeCount;
    }

    /**
     * Execute the job.
     */
    public function handle(RecipeGenerator $generator): void
    {
        try {
            // AIへのプロンプト実行
            $data = $generator->generate(
                $this->ingredients,
                $this->recipeCount
            );

            if (empty($data['recipes'])) {
                Cache::put("recipe_{$this->jobId}", [
                    'status' => 'error',
                    'message' => '食材として認識できなかったのじゃ'
                ], now()->addMinutes(10));

                return;
            }

            // キャッシュに保存（有効期限10分）
            Cache::put("recipe_{$this->jobId}", [
                'status' => 'completed',
                'recipes' => $data['recipes'] ?? []
            ], now()->addMinutes(10));

        } catch (\Exception $e) {
            $msg = $e->getMessage();

            /** debug用 */
            \Log::error('Recipe Job Error', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // エラー内容に応じたメッセージの振り分け
            if (
                str_contains($msg, 'RateLimitedException') ||
                str_contains($msg, 'rate limited') ||
                str_contains($msg, '429')
            ) {
                $errorMessage = '無料枠制限でおじゃる';
            } elseif (str_contains($msg, 'timeout')) {
                $errorMessage = '思考に時間がかかりすぎたのじゃ…もう一度試してたもれ';
            } elseif (str_contains($msg, 'schema')) {
                $errorMessage = '献立の形が崩れてしまったようじゃ';
            } else {
                $errorMessage = 'これまた珍妙なエラーが起きたのじゃ';
            }

            Cache::put("recipe_{$this->jobId}", [
                'status' => 'error',
                'message' => $errorMessage
            ], now()->addMinutes(10));
        }
    }
}
