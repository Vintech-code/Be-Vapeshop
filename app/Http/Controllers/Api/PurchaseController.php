<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
  // In PurchaseController.php

public function index()
{
    $purchases = Purchase::with('cashier')
        ->latest()
        ->paginate(10);

    // Transform each purchase for frontend use
    $data = $purchases->getCollection()->transform(function ($purchase) {
    return [
        'id' => $purchase->id,
        'created_at' => $purchase->created_at,
        'cashier_id' => $purchase->cashier_id,
        'cashier_name' => $purchase->cashier?->name ?? 'N/A',
        'payment_method' => $purchase->payment_method,
        'total_amount' => $purchase->total_amount,
        'cash_received' => $purchase->cash_received,
        'change' => $purchase->change,               
        'items' => $purchase->items,
    ];
});


    return response()->json([
        'data' => $data,
        'meta' => [
            'current_page' => $purchases->currentPage(),
            'last_page' => $purchases->lastPage(),
            'per_page' => $purchases->perPage(),
            'total' => $purchases->total(),
        ]
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cashier_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'cash_received' => 'nullable|numeric|min:0',
            'change' => 'nullable|numeric|min:0',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $purchase = Purchase::create([
            'cashier_id' => $validated['cashier_id'],
            'total_amount' => $validated['total_amount'],
            'payment_method' => $validated['payment_method'],
            'cash_received' => $validated['cash_received'] ?? null,
            'change' => $validated['change'] ?? null,
            'items' => $validated['items'],
        ]);

        return response()->json($purchase, 201);
    }

    public function show(Purchase $purchase)
    {
        return response()->json($purchase->load('user'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return response()->json(null, 204);
    }
}