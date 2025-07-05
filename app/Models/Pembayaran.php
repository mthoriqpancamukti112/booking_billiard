<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    // Definisikan primary key jika berbeda dari 'id'
    protected $primaryKey = 'pembayaran_id';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'booking_id',
        'reference_code',
        'jumlah',
        'status',
        'metode_pembayaran',
        'issuer',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
