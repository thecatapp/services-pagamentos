<?php

namespace App\Http\Services;

use App\Enum\EnumMensagensDeErro;
use App\Enum\EnumSaldo;
use App\Enum\EnumTipoTransferencia;
use App\Enum\EnumUser;
use App\Exceptions\TransferenciaException;
use App\Integrations\RabbitMQ;
use App\Models\Saldo;
use App\Models\Transferencia;
use App\Models\Transferencia_item;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ServicesTransferencia
{
    public function __construct(
        protected Transferencia $Transferencia,
        protected  Transferencia_item $TransferenciaItem,
        protected \App\Entities\Transferencia $TransferenciaEntity
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

    public function salvarNovoSaldo(Transferencia $Transferencia)
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

    public function enviarDadosParaFilaDeProcessamento(array $dados)
    {
        $Rabbit = new RabbitMQ();

        $Rabbit->sendQueue("fila_transferencia", $dados);
    }

    public function receberValoresTransferidos(\stdClass $dados)
    {
        DB::beginTransaction();

        try {

            Arr::map($dados->data->transferencias, function($item) use($dados){

                $Transferecia = new \App\Entities\Transferencia();

                $arrayItem = (array) $item;

                $Transferecia->elaborarObjeto($arrayItem);

                if (!$Transferecia->infoError()){

                    $SaldoPessoa = $this->recuperarSaldoAtualPorPessoa($arrayItem["pessoa_id"]);

                    $this->atualizarSaldoPorPessoa($SaldoPessoa, $arrayItem["valor"]);

                    DB::commit();
                }
            });
        } catch (\Throwable $Throwable){

            DB::rollBack();

            $this->reembolsarTransferencia($dados);
        }
    }

    private function recuperarSaldoAtualPorPessoa(int $pessoaId)
    {
        return Saldo::where("bo_ativo", 1)->where("pessoa_id", $pessoaId)->first();
    }

    private function atualizarSaldoPorPessoa($SaldoPessoa, mixed $valor)
    {

        $SaldoPessoa->bo_ativo = EnumSaldo::DESATIVADO->value;

        $SaldoPessoa->save();

        Saldo::create(
            [
                "vl_saldo" => $valor + $SaldoPessoa->vl_saldo,
                "pessoa_id" => $SaldoPessoa->pessoa_id,
                "bo_ativo" => EnumSaldo::ATIVO->value
            ]
        );
    }

    public function reembolsarTransferencia($item): void
    {
        $this->criarTransferenciaTipoReembolso($item);
        $this->reembolsarValor($item);
    }

    private function criarTransferenciaTipoReembolso($item): void
    {
        $this->Transferencia::create(
            [
                "vl_total" => $item->data->valorTotal,
                "pessoa_origem" => EnumUser::ADMINISTRADOR->value,
                "tp_transferencia" => EnumTipoTransferencia::REEMBOLSO->value
            ]
        );
    }

    private function reembolsarValor($item): void
    {
        $Saldo = Saldo::where("pessoa_id", $item->data->pessoa_origem)->where("bo_ativo", EnumSaldo::ATIVO->value)->first();

        $Saldo->vl_saldo = $Saldo->vl_saldo + $item->data->valorTotal;

        $Saldo->save();
    }


}