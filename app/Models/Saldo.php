<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "vl_saldo",
        "pessoa_id",
        "created_at",
        "updated_at"
    ];

    protected $casts = [
        "id" => "integer",
        "vl_saldo" => "float",
        "pessoa_id" => "integer",
        "created_at" => "date:d-m-Y",
        "updated_at" => "date:d-m-Y"
    ];
}
