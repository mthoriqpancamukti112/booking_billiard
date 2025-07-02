<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    protected $fillable = ['user_id', 'meja_id', 'waktu_mulai', 'waktu_selesai', 'durasi_menit', 'total_biaya', 'status_booking'];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function meja()
    {
        return $this->belongsTo(MejaBilliard::class, 'meja_id');
    }
}
