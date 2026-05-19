<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    
    public function index()
    {
        $orders = Order::with('products')
            ->where('user_id', auth()->id())->get();

        return response()->json([
            'status' => true,
            'data'   => $orders,
        ]);
    }

  
    public function show($id)
    {
        $order = Order::with('products')
            ->where('user_id', auth()->id())
            ->find($id);


        return response()->json([
            'status' => true,
            'data'   => $order,
        ]);
    }


    public function store()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();


        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'status'  => 'Pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                ProductOrder::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'name'       => $item->product->name,
                    'price'      => $item->product->price,
                    'quantity'   => $item->quantity,
                ]);
            }

        
            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'تم إنشاء الطلب بنجاح',
                'data'    => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء إنشاء الطلب',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', auth()->id())->find($id);

      

        if ($order->status !== 'Pending') {
            return response()->json([
                'status'  => false,
                'message' => 'لا يمكن إلغاء الطلب بعد تأكيده',
            ], 422);
        }

        $order->update(['status' => 'Cancelled']);

        return response()->json([
            'status'  => true,
            'message' => 'تم إلغاء الطلب',
        ]);
    }
}