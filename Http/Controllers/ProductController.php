<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Product, Category};
use Illuminate\View\View;

class ProductController extends Controller
{
    //


    public function index($slug = null): View
    {
        $query = $slug ? Category::whereSlug($slug)->firstOrFail()->products() : Product::query();
        $products = $query->get();
        $categories = Category::all();
        return view('products.index', compact('products', 'categories', 'slug'));
    }

    public function show(Product $product): View
    {
        $category = $product->category->name;
        return view('products.show', compact('product', 'category'));
    }



}
