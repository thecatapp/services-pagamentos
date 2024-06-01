<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastroPessoaRequest;
use App\Http\Services\ServicesPessoa;


class PessoaController extends Controller
{

    public function __construct(protected ServicesPessoa $ServicesPessoa){}

    public function cadastrarPessoa(CadastroPessoaRequest $request)
    {

        dd($request->all());


    }
}
