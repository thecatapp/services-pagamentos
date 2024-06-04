<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia_item extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "pessoa_destino",
        "transferencia_id",
        "vl_transferencia",
        "created_at",
        "updated_at",
    ];
    protected $casts = [
        "id" => "integer",
        "pessoa_destino" => "integer",
        "transferencia_id" => "integer",
        "vl_transferencia" => "float",
        "created_at" => "date:d-m-Y",
        "updated_at" => "date:d-m-Y",
    ];
}
