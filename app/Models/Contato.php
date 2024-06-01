<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "email",
        "pessoa_id",
        "created_at",
        "updated_at"
    ];

    protected $cast = [
        "id" => "integer",
        "email" => "string",
        "pessoa_id" => "integer",
        "created_at" => "date:d-m-Y",
        "updated_at" => "date:d-m-Y",
    ];


}
