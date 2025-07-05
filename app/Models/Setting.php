<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Menentukan primary key untuk model.
     * Karena kita menggunakan 'key' sebagai primary key, bukan 'id'.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * Menunjukkan bahwa primary key bukan auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Menentukan tipe data dari primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];
}
