<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebBrand extends Model
{
    use HasFactory;
    protected $table = 'web_brands';
    // public function products(){
    //     return $this->hasMany(WebProduct::class);
    // }
}
