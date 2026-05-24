<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
public function index()
{
    $category = Category::select('id', 'name')->get();
    
    return response()->json([
        'status' => true,
        'data'   => $category,
    ]);
}
public function show($id)
{
 $category = Category::findOrFail($id);
 $products=$category->product;
  return response()->json([
        'status' => true,
        'data'   => $products,
    ]);
}
}
