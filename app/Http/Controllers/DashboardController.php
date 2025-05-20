<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function productStats()
    {
        return [
            'total' => Product::count(),
            'active' => Product::where('is_hidden', false)->count(),
            'hidden' => Product::where('is_hidden', true)->count(),
            'outOfStock' => Product::where('stock', 0)->count(),
        ];
    }

    public function recentProducts()
    {
        $products = Product::orderByDesc('created_at')->take(10)->get();

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category, // or $product->category->name if you have relation
                'price' => $product->price,
                'stock' => $product->stock,
                'is_hidden' => $product->is_hidden,
            ];
        });
    }

    public function categoryCounts()
    {
        $counts = Product::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        return [
            'labels' => $counts->pluck('category'),
            'data' => $counts->pluck('total'),
        ];
    }

    public function stockHistory()
    {
        // Example: total stock per day for last 7 days
        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $labels[] = $date;
            $stock = Product::whereDate('created_at', '<=', $date)->sum('stock');
            $data[] = $stock;
        }
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    
}
