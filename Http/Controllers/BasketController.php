<?php

namespace App\Http\Controllers;

use App\Http\Repositories\BasketInterfaceRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;


class BasketController extends Controller
{
    protected $basketRepository; // L'instance BasketSessionRepository

    public function __construct (BasketInterfaceRepository $basketRepository) {
        $this->basketRepository = $basketRepository;
    }

# Afficher le contenu du panier
    public function show (): View {
        
            //verifier si l'utilisateur est connecté, si le champ customer_id est non nul et si un customer avec cet id existe
            //verif pour récupérer les infos du customer
            if (Auth::check()&&Auth::user()->customer_id!=null&&Customer::where('id',Auth::user()->customer_id)->exists()){
                $customer = Customer::findOrFail(Auth::user()->customer_id);
                //$autreObjet = AutreObjet::findOrFail($id);
                return view('basket.show',  compact('customer'));
            }
            else{
                return view('basket.show');
            }
        
    }


# Ajouter/Mettre à jour un produit du panier
    public function add (Product $product, Request $request) {
        // Validation de la requête
        $this->validate($request, [
            "quantity" => "numeric|min:1"

        ]);

        $basket = session()->get("basket",[]);
        
        // Vérifier si la quantité demandée est disponible
        if($request->quantity>$product->quantity)
        {
            return back()->withMessage('La quantité demandée n\'est pas disponible.');
        }

        // Ajout/Mise à jour du produit au panier avec sa quantité
        $this->basketRepository->add($product, $request->quantity);
        // Redirection vers le panier avec un message  
        return back()->withMessage('Le produit a été ajouté au panier.') ;
    }



    public function remove(Product $product)
    {
        // Suppression du produit du panier par son identifiant
        $this->basketRepository->remove($product);

        // Redirection vers le panier
        return back()->withMessage("Produit retiré du panier");
    }
    public function empty ()
    {
        // Suppression des informations du panier en session
        $this->basketRepository->empty();

        // Redirection vers le panier
        return back()->withMessage("Vous avez vidé votre panier");

    }


}
