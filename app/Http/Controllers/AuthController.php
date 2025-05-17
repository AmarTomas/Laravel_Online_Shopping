<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login()
    {
        return view('front.account.login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register()
    {
        return view('front.account.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            session()->flash('success', 'You have been registerd successfully');

            return response()->json([
                'status' => true,
                'errors' => 'You have been registerd successfully',
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
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                if (session()->has('url.intended')) {
                    return redirect(session()->get('url.intended'));
                }

                return redirect()->route('account.profile');
            } else {
                // session()->flash('error', 'Either email/password is incorrect');

                return redirect()->route('account.login')
                    ->withInput($request->only('email'))
                    ->with('error', 'Either email/password is incorrect');
            }

            session()->flash('success', 'You have been registerd successfully');

            return response()->json([
                'status' => true,
                'errors' => 'You have been registerd successfully',
            ]);
        } else {

            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function profile()
    {
        $userId = Auth::user()->id;
        $user = User::where('id', $userId)->first();
        $countries = Country::orderBy('name', 'ASC')->get();
        $customerAddress = CustomerAddress::where('user_id', $userId)->first();

        return view('front.account.profile', [
            'user' => $user,
            'countries' => $countries,
            'customerAddress' => $customerAddress,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            'phone' => 'required',
        ]);
        if ($validator->passes()) {
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            session()->flash('success', 'Profile Updated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Profile Updated successfully.',
            ]);

            // code...
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

  

    /**
     * Update the specified resource in storage.
     */
    public function logout(Request $request)
    {
        // Auth::logout();
        Auth::guard('web')->logout(); // استخدام SessionGuard

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('account.login')
            ->with('success', 'You successfully logged out');
    }

  

 

    public function wishlist()
    {
        $wishlists = Wishlists::where('user_id', Auth::user()->id)->with('product')->get();
        $data['wishlists'] = $wishlists;

        return view('front.account.wishlist', $data);
    }

    public function showChangePasswordForm()
    {
        return view('front.account.changepassword');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->passes()) {
            $user = User::select('id', 'password')->where('id', Auth::user()->id)->first();
            if (! Hash::check($request->old_password, $user->password)) {
                session()->flash('error', 'You old password is incorrect, please try again.');

                return response()->json([
                    'status' => true,

                ]);
            }

            User::where('id', $user->id)->update([
                'password' => Hash::make($request->new_password),
            ]);
            session()->flash('success', 'You have successfully changed your password.');

            return response()->json([
                'status' => true,

            ]);

        } else {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function forgotPassword()
    {
        return view('front.account.forgot-password');
    }

  

}