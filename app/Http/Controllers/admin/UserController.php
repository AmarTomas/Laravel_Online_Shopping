<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::latest();
        if (! empty($request->get('keyword'))) {
            $users = $users->where('name', 'like', '%'.$request->get('keyword').'%');
            $users = $users->orwhere('email', 'like', '%'.$request->get('keyword').'%');
            // code...
        }
        $users = $users->paginate(10);

        return view('admin.users.show', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('admin.users.create');
    }

   }