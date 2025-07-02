<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;
    protected $table = 'pelanggans';
    protected $primaryKey = 'pelanggan_id';
    protected $fillable = ['user_id', 'nama_lengkap', 'jenis_kelamin', 'nomor_telepon'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
