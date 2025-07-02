<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $table = 'admins';
    protected $primaryKey = 'admin_id';
    protected $fillable = ['user_id', 'nama_admin', 'alamat', 'no_hp'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
