<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $guarded = [];

    function categories()
    {
        return $this->hasMany(Category::class);
    }

    function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    function variations()
    {
        return $this->hasMany(Variation::class);
    }
}
