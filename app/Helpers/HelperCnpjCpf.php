<?php

namespace App\Helpers;

class HelperCnpjCpf
{
    public static function removerCaracteres($string): string
    {
        $valor = trim($string);
        return str_replace(['.', '-', '/'], "", $valor);
    }
}