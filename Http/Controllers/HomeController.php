<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //fonction pour rediriger les utilisateurs vers la bonne page d'accueil en fonction de leur profile
    public function index(){
        if(Auth::id()){
            //utilisateur connecté
            $usertype=Auth()->user()->isAdmin;
            if($usertype==0){
                $products = Product::inRandomOrder()->limit(5)->whereNotIn('name', ["Produit à venir"])->get();
                return view('dashboard' ,compact('products'));
            }
            else if($usertype==1){
                //admin
                $orders = Order::all();
                return view('admin.adminhome', compact('orders')); 
            }  
        }
        else{
            //utilisateur invité
            $products = Product::inRandomOrder()->limit(5)->whereNotIn('name', ["Produit à venir"])->get();
            return view ('welcome',compact('products'));
        }

    }

}
