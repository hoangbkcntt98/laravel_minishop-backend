<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Response\Response;
use App\Status\Status;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private $pancakeURL = "https://pos.pages.fm/api/v1/shops/2254195/";
    private $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiI1NmNkNTYwNS1mZWU0LTQ4NDAtODI3NS1iY2M4YTlmZjZiOGMiLCJpYXQiOjE2MzU2NzM4ODksImZiX25hbWUiOiJkZW1vIGRlbW8iLCJmYl9pZCI6bnVsbCwiZXhwIjoxNjQzNDQ5ODg5fQ.1IZ5KaD3W5M439yefj-scqHGFlONK9gOkSGKLAKVbz0";
    public function removeFields($objList,$arr){
        $temp = $objList;
        for($i =0 ;$i<count($temp);$i++){
            foreach($arr as $field){
                unset($temp[$i][$field]);
            }
        }
        return $temp;
    }
    public function getField($objList,$fields)
    {
        $temp = $objList;

        # code...
    }
    public function getAll(Request $request)
    {
        $res = [];
        try {
            // get all product
            $page = $request->query('page')?$request->query('page'):1;
            $pageSize = $request->query('page_size')?$request->query('page_size'):30;

            $products = Http::get($this->pancakeURL."products", [
                'access_token' => $this->token,
                'page' => $page,
                'page_size' => $pageSize,
                'nearly_out_of_stock' => false,
                'sell_fast' => false,
                'sell_slow' => false,
                'limit_quantity_to_warn' => false,
                'last_imported_price' => false,
            ]);
            $total_pages = $products['total_pages'];
            $products = $products['data'];
            // $products=collect($products);
            $products = array_map(function($element){
                $properties = $element['product_attributes'];
                $colors = $properties[0]['values'];
                $sizes = [];
                // dd($properties);
                if(isset($properties[1])){
                    $sizes = $properties[1]['values'];
                }
                $variations = $element['variations'];
                $variations = array_map(function($variation){
                    // dd($variation);
                    $size = [];
                    if(isset($variation['fields'][1]['value'])){
                        $size = $variation['fields'][1]['value'];
                    }
                    return [
                        'custom_id' => $variation['custom_id'],
                        'display_id' => $variation['display_id'],
                        'color' =>$variation['fields'][0]['value'],
                        'size' => $size,
                        'images' => $variation['images'],
                        'retail_price' => $variation['retail_price'],
                        'weight' => $variation['weight'],
                        'wholesale_price' => $variation['wholesale_price']
                    ];
                },$variations);
                
                return [
                    'name'=>$element['name'],
                    'display_id' => $element['display_id'],
                    'custom_id' =>$element['custom_id'],
                    'colors' =>$colors,
                    'sizes' =>$sizes,
                    'image' => isset($variations[0]['images'][0])?$variations[0]['images'][0]:null,
                    'variations'=>$variations,
                    // 'price' =>$variations['price']
                ];
            },$products);
            $products['total_pages'] = $total_pages;
            // dd($products);  
            $res = new Response('Get products success',$products,Status::GET_PRODUCT_SUCCESS);
        } catch (Exception $e) {
            $res = new Response('Get product Errors',$e->getMessage(),Status::GET_PRODUCT_FAILE);
        }
        return $res -> createJsonResponse();
    }
}
