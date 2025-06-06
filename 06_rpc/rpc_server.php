<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function fib($n)
{
    if ($n == 0) {
        return 0;
    }
    if ($n == 1) {
        return 1;
    }
    return fib($n - 1) + fib($n - 2);
}

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false, false);

echo " [x] Awaiting RPC requests\n";

$callback = function ($req) use ($channel) {
    $n = intval($req->body);
    echo ' [.] fib(', $n, ")\n";

    $result = fib($n);

    $msg = new AMQPMessage(
        (string) $result,
        ['correlation_id' => $req->get('correlation_id')]
    );

    $channel->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );

    $req->ack();
};

$channel->basic_qos((int)null, 1, null);

$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
