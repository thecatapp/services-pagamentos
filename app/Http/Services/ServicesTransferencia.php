<?php

namespace App\Http\Services;

use App\Enum\EnumMensagensDeErro;
use App\Enum\EnumSaldo;
use App\Exceptions\TransferenciaException;
use App\Integrations\RabbitMQ;
use App\Models\Saldo;
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

    public function criarListaDeTransferencia(array $dados): array
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

    public function validarSaldoDisponivel(float $valorTransferencia): bool
    {
        $saldoDisponivel = Saldo::where("pessoa_id", auth()->user()->pessoa_id)->first()->vl_saldo;

        if ($saldoDisponivel < $valorTransferencia){
            throw new TransferenciaException(EnumMensagensDeErro::SALDO_INSUFICIENTE->value, Response::HTTP_CONFLICT);
        }

        return true;
    }

    public function verificarConexao(): bool
    {
        $Rabbit = new RabbitMQ();

        return $Rabbit->verificarConexao();
    }

    public function salvarTransferencia(array $dadosTransferencia) : Transferencia
    {
        return Transferencia::create(
            [
                "vl_total" => $dadosTransferencia["valorTotal"],
                "pessoa_origem" => auth()->user()->pessoa_id
            ]
        );
    }

    public function salvarItensTransferencia(Transferencia $Transferencia, array $itensTransferencia): void
    {
        Arr::map($itensTransferencia, function(\stdClass $item) use (&$Transferencia){

            Transferencia_item::create(
                [
                    "pessoa_destino" => $item->pessoa_id,
                    "transferencia_id" => $Transferencia->id,
                    "vl_transferencia" =>$item->valor
                ]
            );
        });
    }

    public function salvarNovoSaldo(Transferencia $Transferencia, float $valorTotal)
    {

        $saldoAtual = $this->recuperarSaldoAtual();

        $this->desativarSaldoAnterior();

        Saldo::create(
            [
                "vl_saldo" => $saldoAtual - $Transferencia->vl_total,
                "pessoa_id" => auth()->user()->pessoa_id,
                "bo_ativo" => EnumSaldo::ATIVO->value
            ]
        );

    }

    private function desativarSaldoAnterior(): void
    {
        Saldo::where("bo_ativo", 1)
            ->where("pessoa_id", auth()->user()->pessoa_id)
            ->update(["bo_ativo" => EnumSaldo::DESATIVADO->value]);
    }

    private function recuperarSaldoAtual()
    {
        return Saldo::where("bo_ativo", 1)->where("pessoa_id", auth()->user()->pessoa_id)->first()->vl_saldo;
    }
}