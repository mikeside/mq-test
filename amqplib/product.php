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

    // 开启confirm确认模式
    $mqChannel->confirm_select();
    // 设置消息被成功保存到rabbitmq服务器的处理函数
    $mqChannel->set_ack_handler(function (\PhpAmqpLib\Message\AMQPMessage $message){
        echo 'msg Ack success：' . $message->getBody() . PHP_EOL;
    });
    // 设置消息保存到rabbitmq服务器失败的处理函数
    $mqChannel->set_nack_handler(function (\PhpAmqpLib\Message\AMQPMessage $message){
        echo 'msg Nack success：' . $message->getBody() . PHP_EOL;
    });

    // 生产消息
    for ($i=1;$i<=10;$i++){
        $message = '发送邮件给用户：' . 'user' .$i;
        echo $message . PHP_EOL;

        $msg = new \PhpAmqpLib\Message\AMQPMessage($message);
        $mqChannel->basic_publish($msg, 'direct.sendMail', 'key.sendMail');
    }

    $mqChannel->wait_for_pending_acks(5);

    $mqConn->close();

}catch (Exception $e){
    exit($e->getMessage());
}