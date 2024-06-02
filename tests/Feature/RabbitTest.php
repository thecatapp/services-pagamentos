<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class RabbitTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testEnvioDeMensagemParaFila(): void
    {
        $mensagem = ["have" => "valor"];

        Queue::connection("rabbitmq")->pushRaw(json_encode($mensagem), "nome_da_fila");

        $this->assertTrue(true);
    }

    public function testConsumirFila()
    {
        $result = Queue::connection("rabbitmq")->pop("nome_da_fila");

        Log::info($result->getRawBody());

    }

}
