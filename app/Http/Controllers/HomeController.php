<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $users = 10;

        return view('dashboard', compact('users'));
    }
}
