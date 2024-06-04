<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "vl_total",
        "pessoa_origem",
        "created_at",
        "updated_at",
    ];
    protected $casts = [
        "id" => "integer",
        "vl_total" => "float",
        "pessoa_origem" => "integer",
        "created_at" => "date:d-m-Y",
        "updated_at" => "date:d-m-Y",
    ];
}
