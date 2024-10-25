<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationValue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_variations', 'variation_value_id', 'menu_item_id');
    }

    public function orderItems()
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_variations')->withPivot('price');
    }

    protected function casts(): array
    {
        return [
            'name' => 'array'
        ];
    }
}
