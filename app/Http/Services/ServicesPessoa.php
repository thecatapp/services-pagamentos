<?php

namespace App\Http\Services;

use App\Helpers\HelperTipoPessoa;
use App\Models\Pessoa;

class ServicesPessoa
{

    public function __construct(protected Pessoa $Pessoa)
    {
    }

    public function tratarDadosPessoa(array $dados)
    {
        return [
            "nm_pessoa" => $dados["nm_pessoa"],
            "cpf_cnpj" => $dados["cpf_cnpj"],
            "email" => $dados["email"],
            "tp_pessoa" => HelperTipoPessoa::identificarTipoPessoa($dados["cpf_cnpj"])->value,
        ];
    }
    
    public function cadastrarPessoas(array $dados) : Pessoa
    {

        $result = $this->tratarDadosPessoa($dados);

        return $this->Pessoa->create(
            $result
        );
    }
}