<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastroPessoaRequest;
use App\Http\Services\ServicesPessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class PessoaController extends Controller
{

    public function __construct(protected ServicesPessoa $ServicesPessoa){}

    public function cadastrarPessoa(CadastroPessoaRequest $request)
    {
        try {

            DB::beginTransaction();

            $Pessoa = $this->ServicesPessoa->cadastrarPessoas($request->all());

            $this->ServicesPessoa->cadastrarContato($Pessoa, $request->email);
            $this->ServicesPessoa->cadastrarUser($Pessoa, $request->email);

            $this->ServicesPessoa->iniciarSaldo($Pessoa, $request->saldo_inicial);

            DB::commit();

            return Response::json(
                [
                    "data" => "Cadastro efetuado com sucesso!",
                ], 202
            );


        }catch (\Throwable $Throwable){

            DB::rollBack();

            return Response::json(
                [
                    "data" => $Throwable->getMessage(),
                ], 500
            );

        }

    }
}
