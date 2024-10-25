<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array'
        ];
    }

    function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sizeVariations()
    {
        return $this->hasMany(SizeVariation::class);
    }

    public function variations()
    {
        return $this->belongsToMany(VariationValue::class, 'menu_item_variations', 'menu_item_id', 'variation_value_id');
    }
}
