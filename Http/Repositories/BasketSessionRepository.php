<?php
namespace App\Http\Repositories;

use App\Models\Product;
use App\Http\Repositories\BasketInterfaceRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BasketSessionRepository implements BasketInterfaceRepository {

    # Afficher le panier
    public function show ()  {


        return view("basket.show"); // resources\views\basket\show.blade.php
    }

    # Ajouter/Mettre à jour un produit du panier
    public function add (Product $product, $quantity): void
    {
        // On récupère le panier en session
        $basket = session()->get("basket",[]);
       
        // Les informations du produit à ajouter
        $product_details = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $quantity,
            'id'=>$product->id,
        ];
    
        $basket[$product->id] = $product_details; // On ajoute ou on met à jour le produit au panier
        session()->put("basket", $basket); // On enregistre le panier
          
    }

    # Retirer un produit du panier
    public function remove (Product $product) {
        $basket = session()->get("basket"); // On récupère le panier en session
        unset($basket[$product->id]); // On supprime le produit du tableau $basket
        session()->put("basket", $basket); // On enregistre le panier
    }

    # Vider le panier
    public function empty () {
        session()->forget("basket"); // On supprime le panier en session
    }

}


