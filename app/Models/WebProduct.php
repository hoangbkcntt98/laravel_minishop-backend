<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebProduct extends Model
{
    use HasFactory;
    // one to many
    public function images(){
        return $this->hasMany(WebProductImage::class);
    }
    // public function brand(){
    //     // return 'fu';
    //     return $this->belongsTo(WebBrand::class,'web_brand_id','id');
    // }
    //many to many 
    public function properties(){
        return $this->belongsToMany(WebProductProperty::class,"web_product_property",'web_product_id','web_product_property_id');
    }
    //many to many 
    public function categories(){
        return $this->belongsToMany(WebProductCategory::class,"web_product_category",'web_product_id','web_product_category_id');
    }
    // has many 
    public function variations(){
        return $this->hasMany(WebProductVariation::class);
    }


}
