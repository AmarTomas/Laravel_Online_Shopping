<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharges;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function addToCart(Request $request)
    {

        $product = Product::with('product_images')->find($request->id);
        if ($product == null) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Product Not Found',
                ]
            );

        }
        if (Cart::count() > 0) {
            $cartContent = Cart::content();
            $productAlreadyExist = false;
            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
                if ($productAlreadyExist == false) {
                    Cart::add($product->id, $product->title, 1, $product->price, ['prodcutImage' => (! empty($product->product_images)) ? $product->product_images->first() : '']);
                    $status = true;
                    $message = '<strong>'.$product->title.'</strong> added in your cart successfuly';

                    session()->flash('success', $message);
                } else {
                    $status = false;
                    $message = $product->title.'already added in  cart ';
                }
            }
        } else {
            // Cart is empty
            Cart::add($product->id, $product->title, 1, $product->price, ['prodcutImage' => (! empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title.'Product add in your cart successfuly';
            session()->flash('success', $message);

        }

        return response()->json(
            [
                'status' => $status,
                'message' => $message,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function cart()
    {

        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;

        return view('front.cart', $data);
    }

 }