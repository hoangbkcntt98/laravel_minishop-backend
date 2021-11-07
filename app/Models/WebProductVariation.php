<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebProductVariation extends Model
{
    use HasFactory;
    public function product()
    {
        return $this->belongsTo(WebProduct::class,'web_product_id','display_id');
        # code...
    }
}
