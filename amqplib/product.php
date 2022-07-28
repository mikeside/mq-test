<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * 生产者
 * 生产者只需setName存在的交换机名即可
 * 生产者只需向交换机生产消息，根据routingKey
 */

$connConfig = [
    'host' => '',
    'port' => '',
    'vhost' => '',
    'login' => '',
    'password' => '',
];

try {

    // 连接
    $mqConn = new \PhpAmqpLib\Connection\AMQPStreamConnection($connConfig['host'], $connConfig['port'], $connConfig['login'], $connConfig['password'], $connConfig['vhost']);

    // 通道
    $mqChannel = $mqConn->channel();

    // 生产消息
    for ($i=1;$i<=10;$i++){
        $message = '发送邮件给用户：' . 'user' .$i;
        echo $message . PHP_EOL;

        $msg = new \PhpAmqpLib\Message\AMQPMessage($message);
        $mqChannel->basic_publish($msg, 'direct.sendMail', 'key.sendMail');
    }

    $mqConn->close();

}catch (Exception $e){
    exit($e->getMessage());
}