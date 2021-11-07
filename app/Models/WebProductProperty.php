<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebProductProperty extends Model
{
    use HasFactory;
    public function products()
    {
        return $this->belongsToMany(WebProduct::class,'web_product_property','web_product_property_id','web_product_id');
    }
}
