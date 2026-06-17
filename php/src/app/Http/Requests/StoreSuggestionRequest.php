<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreSuggestionRequest extends FormRequest
{
    /**
     * バリデーション失敗時のリダイレクト先（タブを recipe に固定）
     *
     * @var string|null
     */
    protected $redirect = '/shopping?tab=recipe';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ingredients' => [
                'required',
                'string',
                'max:2000',
                // 同じグラフェム（Unicodeの結合文字列）が3回続かないようにカスタムチェック
                function ($attribute, $value, $fail) {
                    // \X は Unicode グラフェムクラスタにマッチ（複数バイト文字対応）
                    if (preg_match('/(\X)\1\1/u', $value)) {
                        $fail('食材でないものを入力とな？');
                    }
                },
            ],
        ];
    }

    /**
     * バリデーション失敗時に専用のエラーバッグでリダイレクトする
     * 他のタブ／箇所にエラーが表示されるのを防ぐ
     *
     * @param Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = redirect()->route('shopping.index', ['tab' => 'recipe'])
            ->withErrors($validator, 'suggestion')
            ->withInput();

        throw new ValidationException($validator, $response);
    }

    public function messages(): array
    {
        return [
            'ingredients.required' => '食材を入力せずに献立提案とな？',
            'ingredients.max'      => '一度に入力できるのは2000文字まででおじゃる。',
        ];
    }
}
