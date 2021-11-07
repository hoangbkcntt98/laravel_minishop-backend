<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebProductCategory extends Model
{
    use HasFactory;
    public function products()
    {
        return $this->belongsToMany(WebProduct::class,'web_product_category','web_product_category_id','web_product_id');
        # code...
    }
}
