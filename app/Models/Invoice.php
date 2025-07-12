<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_name',
        'invoice_receipt_number',
        'uploader_name',
        'file_path',
        'user_id',
    ];

    protected $dates = ['status_updated_at', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
protected $casts = [
    'status_updated_at' => 'datetime',
];
}

