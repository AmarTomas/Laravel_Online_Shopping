<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use App\Models\Carousel;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mail;

class FrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalProducts = Product::count();
        $product = Product::where('is_featured', 'Yes')->orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        $data['featuredProducts'] = $product;
        $product = Product::orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        $carouselItems = Carousel::all();
        $data['latestProducts'] = $product;
        $data['carouselItems'] = $carouselItems;

        return view('front.home', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addToWishlist(Request $request)
    {
        if (Auth::check() == false) {
            session(['url.intended' => url()->previous()]);

            return response()->json([
                'status' => false,
            ]);
        }
        Wishlists::updateOrCreate([
            'user_id' => Auth::user()->id,
            'product_id' => $request->id,
        ], [
            'user_id' => Auth::user()->id,
            'product_id' => $request->id,
        ]);

        $product = Product::where('id', $request->id)->first();
        if ($product == null) {
            return response()->json([
                'status' => true,
                'message' => '<div class="alert-danger">Product not found.</div>',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => '<div class="alert-success"><strong>"'.$product->title.'"</strong> added in your wishlist.</div>',
        ]);
    }

   }