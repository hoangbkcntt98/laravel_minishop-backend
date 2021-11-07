<?php

namespace App\Http\Controllers;

use App\Models\WebBrand;
use App\Models\WebProduct;
use App\Models\WebProductCategory;
use App\Models\WebProductImage;
use App\Models\WebProductProperty;
use App\Models\WebProductVariation;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $variations = [
            [
                'name'=>'Ao loai 1',
                'web_product_id' => 1
            ],
            [
                'name'=>'Ao loai 2',
                'web_product_id' => 1
            ],
            [
                'name'=>'Quan loai 3',
                'web_product_id' => 2
            ],
        ];
        $product_test = [
            [
                'name' => 'Product 1',
                // 'price' => 1000,
                // 'web_brand_id' => 'brand_1',
                'display_id' =>1
            ],
            [
                'name' => 'Product 2',
                // 'price' => 2000,
                // 'web_brand_id' => 'brand_2',
                'display_id' =>2
            ],
            [
                'name' => 'Product 3',
                // 'price' => 2000,
                // 'web_brand_id' => 'brand_1',
                'display_id' =>3
            ],

        ];
        $img_test = [
            [
                'url' => 'http1',
                'web_product_id' => 1
            ],
            [
                'url' => 'http2',
                'web_product_id' => 2
            ]
        ];
        $cate_test = [
            [
                'parent_id' => 0,
                'description' => "abc",
                'name' => 'Loai 1',
            ],
            [
                'parent_id' => 1,
                'description' => "abc",
                'name' => 'Loai 2',
            ],

        ];
        $cate_pro = [
            [
                'web_product_category_id' => 1,
                'web_product_id' => 1
            ],
            [
                'web_product_category_id' => 1,
                'web_product_id' => 2
            ],
            [
                'web_product_category_id' => 2,
                'web_product_id' => 2
            ],
        ];
        $prop_pro = [
            [
                'web_product_property_id' => 1,
                'web_product_id' => 1
            ],
            [
                'web_product_property_id' => 2,
                'web_product_id' => 2
            ],
            [
                'web_product_property_id' => 2,
                'web_product_id' => 1
            ],
        ];
        $properties_test = [
            [
                'parent_id' => 0,
                'description' => "abc",
                'name' => 'Mau sac',
            ],
            [
                'parent_id' => 1,
                'description' => "abc",
                'name' => 'Xanh',
            ],
            [
                'parent_id' => 1,
                'description' => "abc",
                'name' => 'Cam',
            ],
        ];

        try {
            $this->resetData();
            WebProduct::insert($product_test);
            WebProductImage::insert($img_test);
            
            WebProductCategory::insert($cate_test);
            WebProductProperty::insert($properties_test);
            WebProductVariation::insert($variations);

            DB::table('web_product_property')->insert($prop_pro);
            DB::table('web_product_category')->insert($cate_pro);

            //test iamges ok 
            // test brand ok
            //test product cate ok 
            // test product property ok
            //test variation ok
            $product = WebProduct::find(1);
            $property = WebProductProperty::find(2);
            $variation = WebProductVariation::find(1);
            dd($variation->product);
            // dd($property->products);
            dd($product->variations);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
    public function resetData()
    {
        WebProductVariation::query()->delete();
        WebBrand::query()->delete();
        WebProductImage::query()->delete();
        WebProduct::query()->delete();
        WebProductCategory::query()->delete();
        WebProductProperty::query()->delete();
        DB::statement("SET foreign_key_checks=0");
        WebProductVariation::truncate();
        WebBrand::truncate();
        WebProductImage::truncate();
        WebProduct::truncate();
        WebProductCategory::truncate();
        WebProductProperty::truncate();
        DB::statement("SET foreign_key_checks=1");
    }
}
