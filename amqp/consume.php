<?php

/**
 * 消费者
 * 消费者只需要setName队列名即可达到监听队列，从而达到消费队列中的消息（前提是该队列已经创建并绑定交换机）
 * 一般都是将交换机和队列、绑定操作初始化完成
 */

$connConfig = [
    'host' => '',
    'port' => '',
    'vhost' => '',
    'login' => '',
    'password' => '',
    // 心跳检测
    'heartbeat' => 60
];

try {

    // 连接
    $mqConn = new AMQPConnection($connConfig);
    $mqConn->connect();

    // 通道
    $mqChannel = new AMQPChannel($mqConn);

    // 队列消费
    $mqQueue = new AMQPQueue($mqChannel);
    $mqQueue->setName('direct.sendMail1');

    // 执行消费
    $mqQueue->consume(function (AMQPEnvelope $envelope, AMQPQueue $queue){

        sleep(5);
        echo $envelope->getBody() . ' 消费完成✅' . PHP_EOL;
        $queue->ack($envelope->getDeliveryTag());
    });

    $mqConn->disconnect();

}catch (Exception $e){
    exit($e->getMessage());
}