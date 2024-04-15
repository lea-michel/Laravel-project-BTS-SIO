<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Delivery_Addresse;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_item;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // Afficher la liste des commandes pour l'admin
        //  $orders = Order::all();
        //  return view('order.index', compact('orders'));

    }

    /**
     *formulaire qui crée la commande avec adresse de livraison
     */


     public function create()
    {
    //On récupère le panier en session pour afficher un récapitulatif du panier dans la vue
    //$basket = session()->get("basket",[]);
    return view('order.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        


        $request->validate([
            'payment_type'=>'required',
        ]);

        try{
            // dd(session()->all());
            // 'customer_id'
            $request->merge(['customer_id' => session()->get("billing_address.id")]);
            // 'delivery_addresses_id'
            $request->merge(['delivery_addresses_id' => session()->get("delivery_address.id")]);

            //récupérer le total du panier qui est dans la session 
            $basket=session()->get("basket", []);
            $total_basket=0;
            foreach($basket as $product){
                $total_basket+=doubleval($product['price'])*intval($product['quantity']);
            }
            $request->merge(['total' => $total_basket]);

            //création de l'objet Order que l'on enverra à la vue suivante
            $order=Order::create($request->all());
           

            //créer Order_items object avec order_id, product_id et quantity
            foreach($basket as $product){
                //verifier si le produit en session existe dans la base de données
                if(Product::findOrFail($product['id'])){
                    $product_id=intval($product['id']);
                    $quantity=intval($product['quantity']);
                    //création de chaque objet order_item
                    Order_item::create([
                        'order_id' =>$order->id,
                        'product_id' =>$product_id,
                        'quantity' =>$quantity,
                    ]);

                    //dd($items);
                    //décrémenter la quantité commandée pour chaque produit
                    Product::findOrFail($product['id'])->decrement('quantity', $quantity );   
                }    
            }

            //vider la session de l'utilisateur
            //$request->session()->flush();

            //rediriger l'utilisateur en fonction de son moyen de paiement
            if($order->payment_type==="cheque"){
                $request->session()->flush();
                // Appeler la méthode generatePdf(Order $order) du PdfController
                return redirect()->route('generatePDF', compact('order'));
                // $pdfController = new PdfController();
                // return $pdfController->generatePdf($order);
                //return redirect()->route('pdf.generatePDF', compact('order'));
            }elseif($order->payment_type==="paypal"){
                $request->session()->flush();
                return redirect()->away('https://www.paypal.com/fr/home');
            }

            
        }catch(\Exception $e){
                $request->session()->flush();
                return back()->withError($e->getMessage());
            };



    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //récupérer toutes les infos de la commande
        return view('order.show',compact('order'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
        try{
            //use the same view as create function
            if(Auth::check()&&Auth::user()->isAdmin==1){
                //$customer = Customer::findOrFail(Auth::user()->customer_id);
                //adresse de livraison
                $delivery_address = Delivery_Addresse::findOrFail($order->delivery_addresses_id);
                //adresse de facturation
                // $billing_address = Customer::findOrFail($order->customer_id);
                //contenu de la commande
                $order_items = Order_item::where('order_id',$order->id)->get();

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

                return view('order.edit', compact('order', 'delivery_address', 'order_array'));
                
                }

        }catch(\Exception $e){
            return back()->withError($e->getMessage());
        }; 

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //validation de la commande par l'admin
        
        $request->validate([
                'status' => 'required',
        ]);
        try{
            if ($request->has('status')){
                $order->update(['status' =>1]);
                return redirect()->route('home')->with('success','Commande mise à jour avec succès');
            }
        } catch(\Exception $e){
            return back()->withError($e->getMessage());
        };
            
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
