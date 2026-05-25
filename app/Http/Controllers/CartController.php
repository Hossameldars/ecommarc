<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
  
    public function index()
    {
        $cart = Cart::with('product:id,name,price,image')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $cart,

        ]);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1',
            'options'    => 'nullable|array',
        ]);

      
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity ?? 1);
        } else {
            $cartItem = Cart::create([
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity ,
                'options'    => $request->options,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'تم إضافة المنتج للسلة',
            'data'    => $cartItem->load('product:id,name,price,image'),
        ], 201);
    }

        public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cartItem = Cart::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        $cartItem->update(['quantity' => $request->quantity]);
        return response()->json([
            'status'  => true,
            'message' => 'تم تعديل الكمية',
            'data'    => $cartItem,
        ]);
    }  
      public function destroy($id)
    {
        $cartItem = Cart::
          where('product_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        $cartItem->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم حذف المنتج من السلة',
        ]);
    }
        public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم تفريغ السلة',
        ]);
    }
}