<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeVariation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    protected function casts()
    {
        return [
            'name' => 'array'
        ];
    }
}
