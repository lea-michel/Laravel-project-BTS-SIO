<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Delivery_Addresse;
use Illuminate\Support\Facades\Session;

class DeliveryAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('delivery_address.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        //dans le cas où le client veut une adresse de livraison différente de l'adresse de facturation
        if(Session::has('billing_address')){
           
                
                $request->validate([
                    'email' => 'required',
                    'forename' => 'required',
                    'surname'=>'required',
                    'add1'=>'required',
                    'add2'=>'nullable',
                    'postcode'=>'required',
                    'city'=>'required',
                    'phone'=>'required',
                    
                ]);
                
                try{
                    $delivery_address = Delivery_Addresse::create($request->all());
                    session()->put('delivery_address', $delivery_address);
                    return redirect()->route('order.create')->with('success','Delivery address completed successfully.');
                } catch(\Exception $e){
                    return back()->withError($e->getMessage());
                };



        };
      



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
