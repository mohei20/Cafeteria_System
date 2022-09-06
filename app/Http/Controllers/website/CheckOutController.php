<?php

namespace App\Http\Controllers\website;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Order;
use App\Models\TransactionOrder;
use Illuminate\Support\Facades\DB;
use Flasher\Prime\FlasherInterface;
use App\Http\Controllers\Controller;
use App\Http\trait\OrderNotificationTrait;
use App\Http\Requests\website\StoreOrderRequest;


class CheckOutController extends Controller
{
    use OrderNotificationTrait;

    public const TAX = 12;
    public function index(){
        return view('website.check-out');
    }
    public function store(StoreOrderRequest $request,FlasherInterface $flasher){
        $all_cart = Cart::select(
            'product_id',
            'quantity',
            DB::raw("price * quantity As sub_total")
        )->where('user_id',Auth()->user()->id)
        ->get();

        $sub_total = $this->get_all_total_of_cart($all_cart);
        $tax = $sub_total / self::TAX;
        $total = $sub_total + $tax;

        $order = Order::create([
            'user_id' => Auth()->user()->id,
            'ref_id' => Auth()->user()->name.'_'.Carbon::now(),
            'sub_total' => $sub_total,
            'tax' => $tax,
            'total' => $total,
            'notes' => $request->notes,
            'phone' => $request->phone
        ]);

        $all_item_in_cart = Auth()->user()->cart;
        foreach ($all_item_in_cart as $cart) {
            $order->products()->syncWithoutDetaching([$cart->product_id => ['quantity' => $cart->quantity]]);
        }
        Cart::destroy($all_item_in_cart->pluck('id'));


        TransactionOrder::create([
            'order_id' => $order->id,
        ]);

        $flasher->addSuccess("Make Successfully with:ref_id: $order->ref_id");

        $this->send_notificatio_order_to_admins($order);
        return redirect()->route('myorder');

    }

    private function get_all_total_of_cart($all_cart){
        $all_total_cart = 0;
        foreach ($all_cart as $cart) {
            $all_total_cart += $cart->sub_total;
        }
        return $all_total_cart;
    }



}
