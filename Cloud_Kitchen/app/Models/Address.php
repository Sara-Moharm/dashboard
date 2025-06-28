<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['customer_id', 'city', 'district', 'street_address'];

    /**
     * Define the relationship with the Customer model
     * An Address belongs to a Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    /**
     * Define the relationship with the Order model
     * An Address can be associated with multiple Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the full address as a string.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return trim("{$this->street_address}, {$this->district}, {$this->city}");
    }
}
