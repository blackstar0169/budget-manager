<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $attributes = [
        'recurrence' => 'unique',
    ];

    protected $fillable = [
        'label', 'amount', 'date', 'recurrence', 'recurrenceNumber'
    ];
}
