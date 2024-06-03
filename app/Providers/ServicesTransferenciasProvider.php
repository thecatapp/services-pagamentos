<?php

namespace App\Providers;

use App\Http\Services\ServicesTransferencia;
use App\Models\Transferencia;
use App\Models\Transferencia_item;
use Illuminate\Support\ServiceProvider;

class ServicesTransferenciasProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ServicesTransferencia::class, function ($app) {

            $Transferencia = new Transferencia();
            $TransferenciaItens = new Transferencia_item();

            $TransferenciaEntity = new \App\Entities\Transferencia();

            return new ServicesTransferencia($Transferencia, $TransferenciaItens, $TransferenciaEntity);
        });
    }
}
