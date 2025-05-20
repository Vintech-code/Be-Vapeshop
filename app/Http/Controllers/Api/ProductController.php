<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
   public function index(Request $request)
{
    if ($request->has('showHidden') && $request->showHidden == 'true') {
        $products = Product::all(); // Fetch all, including hidden
    } else {
        $products = Product::where('is_hidden', false)->get(); // Only active products
    }

    return response()->json($products);
}


    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $imagePath = $request->file('image')->store('products', 'public');
    $validated['image'] = $imagePath;

    $product = Product::create($validated);

    return response()->json([
        'product' => $product,
        'image_url' => asset("storage/{$imagePath}")
    ], 201);
}
    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_hidden' => 'sometimes|boolean'
            
        ]);

        $validated['name'] = $validated['product_name'] ?? $product->name;
        $validated['stock'] = $validated['quantity'] ?? $product->stock;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json(null, 204);
    }
    
}