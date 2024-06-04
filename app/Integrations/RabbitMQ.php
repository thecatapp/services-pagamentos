<?php

namespace App\Integrations;

use Exception;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Ramsey\Uuid\Nonstandard\Uuid;

class RabbitMQ
{
    private $connection_settings;

    private $channel;

    private $connection;

    public function __construct(array $connection_settings = null)
    {
        if (!$connection_settings) {
            $this->connection_settings = [
                'host' => env('RABBITMQ_HOST'),
                'port' => env('RABBITMQ_PORT'),
                'user' => env('RABBITMQ_USER'),
                'pass' => env('RABBITMQ_PASSWORD')
            ];
        }
    }

    public function createConnection()
    {
        if ($this->connection instanceof AMQPSocketConnection) {
            return true;
        }
        $this->connection = new AMQPStreamConnection(
            $this->connection_settings['host'],
            $this->connection_settings['port'],
            $this->connection_settings['user'],
            $this->connection_settings['pass']
        );
        $this->channel = $this->connection->channel();
        return true;
    }

    public function closeConnection()
    {
        $this->channel->close();
        $this->connection->close();
        $this->connection = null;
        $this->channel = null;
        return true;
    }

    public function sendQueue(string $row_name, array $message = null)
    {
        try {
            $this->createConnection();
            $this->channel->queue_declare($row_name, false, false, false, false);
            if (is_array($message)) {

                $payload = [
                    'job' => 'App\Jobs\ConsumirFilaRabbit',
                    'uuid' => (string) Uuid::uuid4(),
                    'data' => $message,
                    'attempts' => 0,
                ];

                $message = json_encode($payload);
            }

            $msg = new AMQPMessage($message);
            $this->channel->basic_publish($msg, '', $row_name);

            $this->closeConnection();
            
            return true;
        } catch (\Exception $e) {

            $this->closeConnection();
            return false;
        }
    }


    public function sendQueuePriority(string $row_name, array $message = null)
    {
        try {
            $this->createConnection();
            $this->channel->queue_declare($row_name, false, false, false, false, false,
                new AMQPTable(['x-max-priority' =>  10])
            );
            if (is_array($message)) {
                $message = json_encode($message);
            }

            $msg = new AMQPMessage($message, ["priority" => 10]);
            $this->channel->basic_publish($msg, '', $row_name);
            return true;
        } catch (\Exception $e) {
            $this->failed("[RabbitMQ] Falha ao Enviar para fila.\nMessage:{$e->getMessage()}.Line:{$e->getLine()}");
            $this->closeConnection();
            return false;
        }
    }

    public function receiveQueue(string $row_name, callable $callback)
    {
        try {
            $this->createConnection();
            $this->channel->queue_declare($row_name, false, false, false, false);
            $this->channel->basic_consume($row_name, '', false, true, false, false, $callback);

            while (count($this->channel->callbacks)) {
                $this->channel->wait();
            }

            $this->closeConnection();
            return true;
        } catch (\Exception $e) {
            $this->closeConnection();
            return false;
        }
    }

    private function failed($msg)
    {
//        Log::critical($msg);
        if (!empty($this->channel) && empty($this->connection)) {
            $this->closeConnection();
        }
        throw new Exception($msg);
    }

    public function __destruct()
    {
        if ($this->connection instanceof AMQPStreamConnection) {
            $this->closeConnection();
        }
    }

    public function verificarConexao()
    {
        try {
            $testConnection = new AMQPStreamConnection(
                $this->connection_settings['host'],
                $this->connection_settings['port'],
                $this->connection_settings['user'],
                $this->connection_settings['pass']
            );
            $testChannel = $testConnection->channel();
            $testChannel->close();
            $testConnection->close();
            return true;
        } catch (\Exception $e) {
            Log::error('Conexão falhou: ' . $e->getMessage());
        }
        return false;
    }
}
