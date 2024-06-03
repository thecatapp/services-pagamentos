<?php

namespace App\Entities;

use App\Entities\Interfaces\InterfaceElaborarObjeto;
use App\Entities\Interfaces\InterfaceError;
use App\Entities\Interfaces\InterfaceValor;
use App\Models\Transferencia_item;
use Exception;
use Illuminate\Support\Arr;

class Transferencia extends AbstractTransferencia implements InterfaceValor, InterfaceError, InterfaceElaborarObjeto,\JsonSerializable
{

    public function infoError(): bool
    {
        return $this->error;
    }

    public function infoValor(): float
    {
        return $this->valor;
    }

    public function elaborarObjeto(mixed $dados): void
    {
        try {

            $this->validarValorTransferencia($dados["valor"]);

            $this->pessoaDestino = $dados["pessoa_id"];
            $this->valor = $dados["valor"];

            $this->error = false;

        }catch (\Throwable $Throwable){

            $this->error = true;
            $this->msgError = $Throwable->getMessage();
        }
    }

    public function infoMessageErro(): string
    {
        return $this->msgError;
    }

    /**
     * @throws Exception
     */
    public function validarValorTransferencia(float $valor): void
    {
        if ($valor <= 0) {
            throw new Exception('O valor da transferência deve ser maior que 0.');
        }
    }

    public function jsonSerialize(): string
    {
        $array = [
            "valor" => $this->valor,
            "pessoa_id" => $this->pessoaDestino,
        ];

        return json_encode($array);
    }

}