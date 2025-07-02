<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;
    protected $table = 'waiting_lists';
    protected $primaryKey = 'antrian_id';
    protected $fillable = ['user_id', 'waktu_masuk', 'status_antrian'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
