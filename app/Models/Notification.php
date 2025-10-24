<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'message',
        'title',
        "user_id",
    ];
    
}
