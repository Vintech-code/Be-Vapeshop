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
        'quantity' => 'sometimes|integer|min:0', // For POS stock deduction
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        'is_hidden' => 'sometimes|boolean'
    ]);

    // Handle image upload first
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        // Store new image
        $validated['image'] = $request->file('image')->store('products', 'public');
    }

    // SPECIAL HANDLING FOR POS STOCK DEDUCTION
    if ($request->has('quantity') && $request->quantity > 0) {
        $deduction = (int)$request->quantity;
        $newStock = $product->stock - $deduction;
        
        if ($newStock < 0) {
            return response()->json([
                'error' => 'Insufficient stock',
                'current_stock' => $product->stock,
                'requested' => $deduction
            ], 400);
        }
        
        $validated['stock'] = $newStock;
    }

    // Handle regular stock updates (direct value setting)
    if ($request->has('stock') && !$request->has('quantity')) {
        $validated['stock'] = (int)$request->stock;
    }

    // Update the product
    $product->update($validated);

    return response()->json([
        'success' => true,
        'product' => $product,
        'image_url' => $product->image ? asset('storage/'.$product->image) : null,
        'message' => 'Product updated successfully'
    ]);
}
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json(null, 204);
    }
     public function restock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $quantity = (int)$request->quantity;
        $product->stock += $quantity;
        $product->save();

        return response()->json([
            'success' => true,
            'product' => $product,
            'message' => "Successfully added {$quantity} units to stock. New stock: {$product->stock}"
        ]);
    }
    
}