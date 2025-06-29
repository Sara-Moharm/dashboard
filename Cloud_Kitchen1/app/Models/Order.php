<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'staff_id',
        'delivery_personnel_id',
        'total_price',
        'status', 
    ];
    protected $guarded = [];

    public function orderItems (): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(OrderItem::class);
    }

    public function updateTotalPrice(){
        $total = $this->orderItems()->sum(DB::raw('quantity * price'));
        $this->update(['total_price' => $total]);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    
    public function deliveredBy(){
        return $this->belongsTo(Staff::class);
    }

    public function updateStatusIfAllItemsAre($targetStatus)
    {
        if ($this->orderItems()->where('status', '!=', $targetStatus)->doesntExist()) {
            $this->update(['status' => $targetStatus]);
        }
    }
    
    public function Address()
    {
        return $this->belongsTo(Address::class);
    }
    protected static function booted(){
        static::updating(function ($order) {
            // ✅ 1. set timestamps for status changes before saving
            if ($order->isDirty('status')) {
                switch ($order->status) {
                    case 'preparing':
                        $order->preparing_at =  now();
                        break;
                    case 'ready':
                        $order->ready_at =  now();
                        break;
                    case 'delivering':
                        $order->delivering_at =  now();
                        break;
                    case 'delivered':
                        $order->delivered_at =  now();
                        break;
                }
            }
        });
    }
}