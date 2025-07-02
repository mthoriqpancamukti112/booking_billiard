<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MejaBilliard extends Model
{
    use HasFactory;
    protected $table = 'meja_billiards';
    protected $primaryKey = 'meja_id';
    protected $fillable = ['nomor_meja', 'tipe_meja', 'status', 'harga_per_jam'];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'meja_id');
    }
}
