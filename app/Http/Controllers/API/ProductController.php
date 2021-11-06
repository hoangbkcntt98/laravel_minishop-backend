<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
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
            // get brand
            $brands = Http::get($this->pancakeURL."brand",[
                'access_token' => $this->token,
            ])['data'];
            $brands =$this->removeFields($brands,['inserted_at','updated_at']);
            Brand::insert($brands);
            $products = $products['data'];
            $res = new Response('Get products success',$products,Status::GET_PRODUCT_SUCCESS);
        } catch (Exception $e) {
            $res = new Response('Get product Errors',$e->getMessage(),Status::GET_PRODUCT_FAILE);
        }
        return $res -> createJsonResponse();
    }
}
