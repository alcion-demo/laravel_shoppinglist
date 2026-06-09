<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Enums\ShopType;

#[Fillable(['shopping_item_id', 'price', 'quantity', 'shop_type'])]
class CurrentCart extends Model
{
    protected $casts = [
        'shop_type' => ShopType::class,
    ];

    /**
     * 商品マスターとのリレーション
     */
    public function item()
    {
        return $this->belongsTo(ShoppingItem::class, 'shopping_item_id');
    }
}
