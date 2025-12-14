<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialTransaction;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categoryCount  = Category::count();
        $materialCount  = Material::count();
        $transactionCount = MaterialTransaction::count();

        return view('dashboard', compact(
            'categoryCount',
            'materialCount',
            'transactionCount'
        ));
    }
}
