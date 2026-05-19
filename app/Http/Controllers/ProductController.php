<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        //$products = Product::latest()->paginate(10);
$products = Product::with('category:id,name')->get();
        return response()->json([
            'status'  => true,
            'data'    => $products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {


    $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
        'price'       => 'required|numeric|min:0',
    ]);

    $path = null; 

    if ($request->hasFile('image')) { 
        $path = $request->file('image')->store('products', 'public');
    }

    $product = Product::create([
        'name'        => $request->name,
        'description' => $request->description,
        'price'       => $request->price,
        'image'       => $path,
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Product created successfully',
        'data'    => $product,
    ], 201);
    }

    public function show($id): JsonResponse
    {
      $product=Product::findorfail($id);
        return response()->json([
            'status' => true,
            'data'   => $product,
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'price'       => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Product updated successfully',
            'data'    => $product,
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
      //  $product->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}