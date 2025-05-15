<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->confirm_select();

$ack_handler = function (AMQPMessage $message) {
    echo " [✓] Message ACKed. Body: " . $message->getBody() . "\n";
};

$nack_handler = function (AMQPMessage $message) {
    echo " [✗] Message NACKed. Body: " . $message->getBody() . "\n";
};

$channel->set_ack_handler($ack_handler);
$channel->set_nack_handler($nack_handler);

$channel->queue_declare('hello_confirm', false, false, false, false);

$messageBody = 'Hello World with Confirms!';
$msg = new AMQPMessage($messageBody);

$channel->basic_publish($msg, '', 'hello_confirm');
echo " [x] Sent '" . $messageBody . "'\n";

try {
    $channel->wait_for_pending_acks(5.000);
    echo " [!] All messages confirmed by broker.\n";
} catch (Throwable $e) {
    echo " [!] Timeout waiting for confirms: " . $e->getMessage() . "\n";
}

$channel->close();
$connection->close();
