<?php

namespace App\Entities;

use App\Enum\EnumTransferenciaValida;

abstract  class AbstractTransferencia
{
    protected int $pessoaDestino;
    protected int $pessoaOrigem;
    protected float $valor;

    protected bool $error = true;
    protected string $msgError = "";

    protected EnumTransferenciaValida $transferenciaValida;

}