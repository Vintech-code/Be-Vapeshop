<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_id',
        'total_amount',
        'payment_method',
        'cash_received',
        'change',
        'items'
    ];

    protected $casts = [
        'items' => 'array',
        'total_amount' => 'float',
    ];

    public function cashier()
{
    return $this->belongsTo(User::class, 'cashier_id');
}

    public function items()
{
    return $this->hasMany(PurchaseItem::class);
}
}