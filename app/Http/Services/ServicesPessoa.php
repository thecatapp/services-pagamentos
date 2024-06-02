<?php

namespace App\Http\Services;

use App\Helpers\HelpersFile;
use App\Helpers\HelperTipoPessoa;
use App\Models\Contato;
use App\Models\Pessoa;
use App\Models\Saldo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ServicesPessoa
{

    public function __construct(protected Pessoa $Pessoa)
    {
    }

    public function tratarDadosPessoa(array $dados): array
    {
        return [
            "nm_pessoa" => $dados["nm_pessoa"],
            "cpf_cnpj" => $dados["cpf_cnpj"],
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

    public function cadastrarContato(Pessoa $Pessoa, $email)
    {

        return Contato::create(
            [
                "email" => $email,
                "pessoa_id" => $Pessoa->id
            ]
        );
    }

    public function cadastrarUser(Pessoa $Pessoa, string $email)
    {
        return User::create([
            'name' => $Pessoa->nm_pessoa,
            'email' => $email,
            'password' => Hash::make('password123'),
            'pessoa_id' =>$Pessoa->id

        ]);
    }

    public function iniciarSaldo(Pessoa $Pessoa, float $saldoInicial)
    {
        Saldo::create(
            [
                "pessoa_id" => $Pessoa->id,
                "vl_saldo" => $saldoInicial
            ]
        );
    }
}