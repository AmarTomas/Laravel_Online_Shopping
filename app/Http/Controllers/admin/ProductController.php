<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = [];
        $products = Product::latest('id')->with('product_images');
        // dd($products);
        if (! empty($request->get('keyword'))) {
            $products = $products->where('title', 'like', '%'.$request->get('keyword').'%');
        }
        $data['products'] = $products->paginate(10);

        return view('admin.products.show', $data);
    }

  

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['brands'] = $brands;
        $data['categories'] = $categories;

        return view('admin.products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
 }