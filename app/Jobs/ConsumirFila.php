<?php

namespace App\Jobs;

use App\Http\Services\ServicesTransferencia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ConsumirFila implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ServicesTransferencia $Transferencia){}

    public function handle()
    {
        Log::error("AQUI");
//        $this->Transferencia->consumirFila();
    }
}
