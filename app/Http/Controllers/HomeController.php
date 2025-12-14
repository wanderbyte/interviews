<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // return view('dashboard');

        $users = 10;

        return view('dashboard', compact('users'));
        // return view('dashboard')->with('usersCount', User::count());

    }
}
