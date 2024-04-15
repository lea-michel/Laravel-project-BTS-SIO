<?php

namespace App\Http\Controllers;

use App\Models\Delivery_Addresse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;



class PDFController extends Controller
{
    //
    public function generatePDF(Order $order)
    {
        try{
        $customer=Customer::findOrFail($order->customer_id);
        //$delivery_add=Delivery_Addresse::findOrFail($order->delivery_addresses_id);
        $order_items= DB::table('order_items')->where('order_id', $order->id)->get();

        //dd($order_items);
        //tableau pour récupérer infos de la commande
        foreach($order_items as $item){
            $find_product = Product::find($item->product_id);
            $product=
                [
                    'name' => $find_product->name,
                    'price'=> $find_product->price,
                    'quantity'=>$item->quantity,
                    'total'=>$item->quantity*$find_product->price,
                ];
            $order_array[]=$product;


        }
        // dd($order_array);
       
        $data=[
            'forename'=>$customer->forename,
            'surname'=>$customer->surname,
            'add1'=>$customer->add1,
            'add2'=>$customer->add2,
            'postcode'=>$customer->postcode,
            'city'=>$customer->city,
            'products'=>$order_array,
            'total'=>$order->total,
            'date'=> date('m/d/Y'),
        ];
        $pdf=PDF::loadView('orderPDF', $data);
        return $pdf->stream('paiement-Woodycraft.pdf');
    
        } catch(\Exception $e){
            return back()->withError($e->getMessage());
        };

    }


}
