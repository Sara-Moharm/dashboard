<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

    protected $fillable = ['customer_id','feedback', 'sentiment'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function aspects()
    {
        return $this->hasMany(FeedbackAspect::class);
    }
}
