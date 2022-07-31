<?php

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
    $mqConn = new AMQPConnection($connConfig);
    $mqConn->connect();

    // 通道
    $mqChannel = new AMQPChannel($mqConn);

    // 交换机
    $mqExchange = new AMQPExchange($mqChannel);
    $mqExchange->setName('direct.sendMail');

    // 生产消息
    for ($i=1;$i<=10;$i++){
        $message = '发送邮件给用户：' . 'user' .$i;
        echo $message . PHP_EOL;

        $mqExchange->publish($message, 'key.delay.sendMail', null, ['delivery_mode' => 2]);
    }

    $mqConn->disconnect();

}catch (Exception $e){
    exit($e->getMessage());
}