<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreShoppingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => '商品名',
            'quantity' => '個数',
            'price' => '価格',
        ];

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'price'    => ['nullable', 'integer', 'min:0' ,'max:999999'],
            'quantity' => [
                'nullable',
                'string',
                // 先頭が必ず半角数字(0-9)で始まることを強制する正規表現
                'regex:/^[0-9]+.*/', 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.regex' => '個数は「1パック」や「2本」のように、必ず数値から入力してください。',
        ];
    }
}
