<?php

namespace App\Entities;

abstract  class AbstractTransferencia
{
    protected int $pessoaDestino;
    protected int $pessoaOrigem;
    protected float $valor;

    protected bool $error = true;
    protected string $msgError = "";

}