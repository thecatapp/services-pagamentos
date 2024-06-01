<?php

namespace App\Http\Services;

use App\Exceptions\TransferenciaException;
use App\Models\Transferencia;
use App\Models\Transferencia_item;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ServicesTransferencia
{
    public function __construct(
        protected Transferencia $Transferencia,
        protected  Transferencia_item $TransferenciaItem,
    ){}

    public function criarListaDeTransferencia(array $dados)
    {
        $listarDeTransferencia = [
            "valorTotal" => 0 ,
            "transferencias" => [],
        ];

        Arr::map($dados["transferencias"], function($item) use (&$listarDeTransferencia){

            $Transferecia = new \App\Entities\Transferencia();

            $Transferecia->elaborarObjeto($item);

            if ($Transferecia->infoError()){
                throw new TransferenciaException($Transferecia->infoMessageErro(), Response::HTTP_BAD_REQUEST);
            }

            $listarDeTransferencia["transferencias"][] = json_decode($Transferecia->jsonSerialize());
            $listarDeTransferencia["valorTotal"] = $listarDeTransferencia["valorTotal"] + $Transferecia->infoValor();

        });

        return $listarDeTransferencia;
    }
}