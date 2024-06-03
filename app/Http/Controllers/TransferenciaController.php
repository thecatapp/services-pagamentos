<?php

namespace App\Http\Controllers;

use App\Http\Services\ServicesTransferencia;
use Illuminate\Http\JsonResponse;
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

    public function transferirValores(Request $request): JsonResponse
    {

        DB::beginTransaction();

        try {

            $listaDeTransferencia = $this->TransferenciaService->criarListaDeTransferencia($request->all());

            $saldoDisponivel = $this->TransferenciaService->validarSaldoDisponivel($listaDeTransferencia["valorTotal"]);

            $conexaoPronta = $this->TransferenciaService->verificarConexao();


            if ($conexaoPronta && $saldoDisponivel) {

                $Transferencia = $this->TransferenciaService->salvarTransferencia($listaDeTransferencia);

                $listaDeTransferencia["pessoa_origem"] = $Transferencia->pessoa_origem;
                
                $this->TransferenciaService->salvarItensTransferencia($Transferencia, $listaDeTransferencia["transferencias"]);

                $this->TransferenciaService->salvarNovoSaldo($Transferencia);

                $this->TransferenciaService->enviarDadosParaFilaDeProcessamento($listaDeTransferencia);

                DB::commit();

                return Response::json(
                    [
                        "data" => "Transferencia criada com sucesso",
                    ], ResponseAlias::HTTP_ACCEPTED
                );

            } else {

                DB::rollBack();

                return Response::json(
                    [
                        "data" => "Por favor, confirme o saldo da conta e tente novamente mais tarde",
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );

            }

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
