<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = "web_brands";
    protected $fillable = [
        'id',
        'display_id',
        'name',
        'description'
    ];
}
