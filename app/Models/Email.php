<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'lang',
        'name',
        'address',
        'subject',
        'body'
    ];

    public function tracker()
    {
        return $this->hasOne(Tracker::class);
    }
}
