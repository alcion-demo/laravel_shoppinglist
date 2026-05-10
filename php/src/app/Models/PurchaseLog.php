<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Enums\ShopType;
use Illuminate\Database\Eloquent\Builder;

#[Fillable(['shopping_item_id', 'price', 'quantity' ,'shop_type', 'purchased_at'])]
class PurchaseLog extends Model
{
    protected $casts = [
        'shop_type' => ShopType::class,
        'purchased_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(ShoppingItem::class, 'shopping_item_id');
    }

    public function getPurchasedDateStringAttribute(): string
    {
        return $this->purchased_at->format('Y-m-d');
    }


}
