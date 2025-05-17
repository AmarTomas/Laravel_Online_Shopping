<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
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
        $countries = Country::get();
        $data['countries'] = $countries;
        $shippingcharges = ShippingCharges::select('shipping_charges.*', 'countries.name')->leftJoin('countries', 'countries.id', 'shipping_charges.country_id')->get();
        $data['shippingcharges'] = $shippingcharges;

        return view('admin.shipping.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->passes()) {
            $count = ShippingCharges::where('country_id', $request->country)->count();
            if ($count > 0) {

                $request->session()->flash('error', 'Shipping already added ');

                return response()->json([
                    'status' => true,
                ]);

            }
            $shipping = new ShippingCharges;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            $request->session()->flash('success', 'Shipping added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Shipping added successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
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
        $countries = Country::get();
        $data['countries'] = $countries;
        $shippingcharges = ShippingCharges::find($id);
        $data['shippingcharges'] = $shippingcharges;
        // dd($shippingcharges);

        return view('admin.shipping.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->passes()) {

            $shipping = ShippingCharges::find($id);
            if (empty($shipping)) {
                $request->session()->flash('error', 'Shipping not found');

                return response()->json([
                    'status' => true,
                ]);
            }

            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            $request->session()->flash('success', 'Shipping updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Shipping updated successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {

        $shipping = ShippingCharges::find($id);

        if (empty($shipping)) {
            $request->session()->flash('error', 'Shipping not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $shipping->delete();
        $request->session()->flash('success', 'Shipping delete successfully');

        return response()->json([
            'status' => true,
            'message' => 'Shipping delete successfully',
        ]);

    }
}
