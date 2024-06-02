<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferenciaRequest;
use App\Http\Requests\UpdateTransferenciaRequest;
use App\Http\Services\ServicesTransferencia;
use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TransferenciaController extends Controller
{
    public function __construct(protected ServicesTransferencia $TransferenciaService)
    {
    }

    public function transferirValores(Request $request)
    {

        DB::beginTransaction();

        try {

            $listaDeTransferencia = $this->TransferenciaService->criarListaDeTransferencia($request->all());

            $saldoDisponivel = $this->TransferenciaService->validarSaldoDisponivel($listaDeTransferencia["valorTotal"]);

            $conexaoPronta = $this->TransferenciaService->verificarConexao();

            if ($conexaoPronta && $saldoDisponivel) {
                $Transferencia = $this->TransferenciaService->salvarTransferencia($listaDeTransferencia);

                $this->TransferenciaService->salvarItensTransferencia($Transferencia, $listaDeTransferencia["transferencias"]);

                $this->TransferenciaService->salvarNovoSaldo($Transferencia, $listaDeTransferencia["valorTotal"]);
            }

            DB::commit();

            return Response::json(
                [
                    "data" => "Transferencia criada com sucesso",
                ], ResponseAlias::HTTP_ACCEPTED
            );

        }catch (\Throwable $Throwable){
            DB::rollBack();

            Log::error($Throwable->getMessage());

            return Response::json(
                [
                    "data" => "Opss, algo de errado aconteceu, contate o suporte ou espera alguns minutos",
                ], ResponseAlias::HTTP_CONFLICT
            );
        }

    }
}
