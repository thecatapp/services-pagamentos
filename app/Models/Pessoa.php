<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    use HasFactory;

    protected $fillable =[
        "nm_pessoa",
        "cpf_cnpj",
        "created_at",
        "updated_at",
        "tp_pessoa"
    ];

    protected $casts = [
        "nm_pessoa" => "string",
        "cpf_cnpj" => "string",
        "tp_pessoa" => "integer",
        "created_at" => "date:d-m-Y",
        "updated_at" => "date:d-m-Y"
    ];
}
