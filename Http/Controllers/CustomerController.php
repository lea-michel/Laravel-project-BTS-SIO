<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Delivery_Addresse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
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
        return view('customer.create');
    }

    /**
     * Store a newly created customer in storage.
     */

    
     public function store(Request $request)
    {
        try{
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

       
            //->except(['_token', '_method' ])
            $customer = Customer::create($request->except('same_as_billing_address'));
            
            // si l'utilisateur est connecté mettre à jour le champ customer_id du user avec l'id du customer créé
            if(Auth::check()&&Auth::user()->customer_id==null){
                Auth::user()->update(['customer_id' => $customer->id]);    
            }
            
            // si l'utilisateur est connecté mettre le champ registered de customer à 1 
            if(Auth::check()){
                $customer->update(['registered' =>1]);
            }
            
            //stocker dans la session les infos du client pour les utiliser plus tard lors de la finalisation de la commande
            session()->put('billing_address', $customer);
            if ($request->has('same_as_billing_address')) {
                //récupérer les infos du client et les utiliser pour l'adresse de livraison
                //$request->merge(['same_as_billing_address' => 1]);
                $delivery_address = Delivery_addresse::create($request->all());
                $delivery_address->same_as_billing_address=1;
                $delivery_address->save();
                session()->put('delivery_address', $delivery_address);
                
               
            //$data = session('billing_address', []);
            //dd(session()->all());
            return redirect()->route('order.create')->with('success','Profile created successfully.');
            };

            return redirect()->route('delivery.create')->with('success','Customer profile created successfully.');    
                        
        } catch(\Exception $e){
            return back()->withError($e->getMessage());
        };

    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $id)
    {
        //


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(customer $customer)
    {
        try{
            //use the same view as create function
            if(Auth::check()){
                //verifier si l'utilisateur est connecté, si le champ customer_id est non nul et si un customer avec cet id existe
                if (Auth::check()&&Auth::user()->customer_id!=null&&Customer::where('id',Auth::user()->customer_id)->exists()){
                    $customer = Customer::findOrFail(Auth::user()->customer_id);
                    return view('customer.edit', compact('customer'));
                    //$files=File::get()->where('question_id', $question->id);
                
                }
            };
        
        }catch(\Exception $e){
            return back()->withError($e->getMessage());
        }; 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
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
            $customer=Customer::where('id', $customer->id)->update($request->all());
            session()::put('billing_address', $customer);
            if ($request->has('same_as_billing_address')) {
                //récupérer les infos du client et les utiliser pour l'adresse de livraison
                //$request->merge(['same_as_billing_address' => 1]);
                $delivery_address = Delivery_addresse::create($request->all());
                $delivery_address->same_as_billing_address=1;
                session()->put('delivery_address', $delivery_address);
                
                //dd(session()->all());
                //rediriger l'utilisateur vers la vue paiement
                return view('order.create',  compact('customer'))
                            ->with('success','Customer profile updated successfully.');

                        } else {
                            //rediriger l'utilisateur vers la vue pour renseigner adresse de livraison
                            return view('delivery_address.create',  compact('customer'))
                            ->with('success','Customer profile created successfully.');    
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
