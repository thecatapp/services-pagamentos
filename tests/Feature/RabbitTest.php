<?php

namespace Tests\Feature;

use App\Integrations\RabbitMQ;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tests\TestCase;
class RabbitTest extends TestCase
{

    protected RabbitMQ | null $RabbitMQ;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->RabbitMQ = new RabbitMQ();

    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        $this->RabbitMQ = null;
    }

    public function testEnvioDeMensagemParaFila(): void
    {
        $mensagem = ["have" => "valor"];

        Queue::connection("rabbitmq")->pushRaw(json_encode($mensagem), "nome_da_fila");

        $this->assertTrue(true);
    }

    public function testConsumirFila()
    {
        $connection = new AMQPStreamConnection(env("RABBITMQ_HOST"), env("RABBITMQ_PORT"), env("RABBITMQ_LOGIN"), env("RABBITMQ_PASSWORD"));
        $channel = $connection->channel();

        $callback = function($msg) {
            Log::info($msg->body);
            // Acknowledge the message
            $msg->ack();
        };

        $channel->basic_consume('fila_transferencia', '', false, false, false, false, $callback);

        // Wait for the message to be consumed
        while($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function testVerificarConexao()
    {
        $result = $this->RabbitMQ->verificarConexao();

        $this->assertTrue($result);
    }


}
