<?php

namespace App\Helpers;

use App\Enum\EnumTipoPessoa;

class HelperTipoPessoa
{
    public static function identificarTipoPessoa(mixed $cpf_cnpj): EnumTipoPessoa
    {
        if (strlen($cpf_cnpj) != 14) {
            return EnumTipoPessoa::PessoaFisica;
        }

        return EnumTipoPessoa::PessoaJuridica;

    }
}