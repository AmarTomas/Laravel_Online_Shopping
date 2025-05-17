<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;

// use Intervention\Image\Drivers\Gd\Driver;
// use Intervention\Image\ImageManager;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        
    }

    public function createMult(Request $request)
    {
        $image = $request->file('image');
        if (empty($image)) {
            return response()->json(['status' => false,
                'message' => 'Image uploaded errroeoeo']);
        } else {
            $ext = $image->getClientOriginalExtension();
            $newName = time().'-'.uniqid().'-'.rand(1000, 9999).'.'.$ext;
            $tempImage = new TempImage;
            $tempImage->name = $newName;
            $tempImage->save();
            $image->move(public_path().'/temp', $newName);

            return response()->json(['status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/temp/'.$newName),
                'message' => 'Image uploaded successfully']);
        }
    }
}


