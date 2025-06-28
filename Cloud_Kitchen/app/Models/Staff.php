<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Staff extends Model
{
    use HasFactory , SoftDeletes , LogsActivity;

    // Fillable fields for mass assignment
    protected $fillable = ['user_id','shift_start', 'shift_end', 'status'];

    /**
     * Define the relationship with the User model
     * A StaffProfile belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prepareOrders()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliverOrders()
    {
        return $this->hasMany(Order::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['shift_start', 'shift_end', 'status']) 
            ->useLogName('staff')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
