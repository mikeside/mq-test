<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * 消费者
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


    // 订阅队列
    $callback = function ($msg){

        if ($msg->body == 'quit'){

            // 停止消费并推出
            $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
        }else {

            sleep(3);

            // 消息确认
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            echo $msg->body . ' 消费完成✅' . PHP_EOL;
        }

    };

    // 没接受上一条消息ask 拒绝接受下一条消息
    $mqChannel->basic_qos(null, 1, null);
    $mqChannel->basic_consume('direct.sendMail2', '', false, false, false,false, $callback);

    // 执行消费
    while (count($mqChannel->callbacks)){

        $mqChannel->wait();
    }

    $mqConn->close();

}catch (Exception $e){
    exit($e->getMessage());
}