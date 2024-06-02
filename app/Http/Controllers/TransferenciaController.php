<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferenciaRequest;
use App\Http\Requests\UpdateTransferenciaRequest;
use App\Http\Services\ServicesTransferencia;
use App\Models\Transferencia;
use Illuminate\Http\Request;

class TransferenciaController extends Controller
{
    public function __construct(protected ServicesTransferencia $TransferenciaService)
    {
    }

    public function transferirValores(Request $request)
    {
        $listaDeTransferencia = $this->TransferenciaService->criarListaDeTransferencia($request->all());
    }
}
