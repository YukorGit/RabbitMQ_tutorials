<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);

$routing_key = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'anonymous.info';
$message = empty($argv[2]) ? 'Hello World!' : implode(' ', array_slice($argv, 2));

$msg = new AMQPMessage($message);

$channel->basic_publish($msg, 'topic_logs', $routing_key);

echo ' [x] Sent ', $routing_key, ':', $message, "\n";

$channel->close();
$connection->close();