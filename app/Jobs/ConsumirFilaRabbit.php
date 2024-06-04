<?php

namespace App\Jobs;

use App\Http\Services\ServicesTransferencia;
use App\Integrations\RabbitMQ;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConsumirFilaRabbit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nmFila = "fila_transferencia";

    /**
     * Create a new job instance.
     */
    public function __construct(protected ServicesTransferencia $ServicesTransferencia){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $callback = function ($msg) {
            $dados = json_decode($msg->body);

            $this->ServicesTransferencia->receberValoresTransferidos($dados);
        };

        $Rabbit = new RabbitMQ();
        $Rabbit->receiveQueue($this->nmFila, $callback);
    }

    public function fire()
    {

        $this->handle();
    }

}
