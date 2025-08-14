<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_id', 
        'sent',
        'opened', 
        'clicks', 
        'unsubscribed',
    ];

    public function email()
    {
        return $this->belongsTo(Email::class);
    }
}
