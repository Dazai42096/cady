<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_name',
        'contact_person',
        'phone',
        'email',
        'service_type',
        'message',
        'status',
    ];
}
