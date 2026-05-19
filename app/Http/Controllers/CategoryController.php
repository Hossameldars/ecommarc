<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        //$products = Product::latest()->paginate(10);
     $category = Category::all();
        return response()->json([
            'status'  => true,
            'data'    => $category,
        ]);
    }
}
