<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $models = User::where('role', '!=', 'admin')->get();
        return view('users',['models' => $models]);
    }
}
