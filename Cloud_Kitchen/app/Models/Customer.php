<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Customer extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    // Fillable fields for mass assignment
    protected $fillable = ['user_id', 'second_phone_number', 'address'];

    /**
     * Define the relationship with the User model
     * A CustomerProfile belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with the Address model
     * A CustomerProfile may have many addresses
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Define the relationship with the feedback model
     * A CustomerProfile may have many feedbacks
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Define the relationship with the Order model
     * A CustomerProfile may place many orders
     */
    public function placesOrders(){
        return $this->hasMany(Order::class);
    }
    
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['second_phone_number', 'address']) 
            ->useLogName('customer')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

}
