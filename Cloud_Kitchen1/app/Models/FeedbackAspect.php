<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackAspect extends Model
{
    protected $table = 'feedback_aspects';

    protected $fillable = ['feedback_id', 'aspect', 'sentence', 'sentiment'];


    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}
