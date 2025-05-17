<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $image = $request->image;
        if (empty($image)) {
            return response()->json(['status' => false,
                'message' => 'Image uploaded errroeoeo']);
        } else {
            $ext = $image->getClientOriginalExtension();
            $newImageName = $request->product_id.'-'.time().'-'.uniqid().'-'.rand(1000, 9999).'.'.$ext;
            $productImage = new ProductImage;
            $productImage->product_id = $request->product_id;
            $productImage->imgae = $newImageName;
            $productImage->save();

            $image->move(public_path().'/uploads/product/', $newImageName);

            return response()->json(['status' => true,
                'image_id' => $productImage->id,
                'ImagePath' => asset('/uploads/product/'.$productImage->imgae),
                'message' => 'Image saved successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $productImage = ProductImage::find($request->id);
        if (empty($productImage)) {
            return response()->json(['status' => false,
                'message' => 'Image not found']);
        }
        File::delete(public_path().'/uploads/product/', $productImage->imgae);

        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image Deleted successfully']);

    }
}
