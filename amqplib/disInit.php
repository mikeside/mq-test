<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 *  删除交换机、队列
 */

$connConfig = [
    'host' => '',
    'port' => '',
    'vhost' => '',
    'login' => '',
    'password' => '',
];

/**
 *  一个交换机类型中可以有多个交换机
 *  一个交换机中可以有多个队列
 *  一个路由键可以被多个队列绑定（就是两个队列绑定的路由键相同），也可以不同
 */
$config = [
    AMQP_EX_TYPE_DIRECT => [
        [
            'exchangeName' => 'direct.sendMail',
            'exchangeFlag' => AMQP_DURABLE,
            'queue' => [
                [
                    'queueName' => 'direct.sendMail1',
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => 'key.sendMail',
                ],
                [
                    'queueName' => 'direct.sendMail2',
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => 'key.sendMail',
                ],
                [
                    'queueName' => 'direct.sendMail3',
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => 'key.sendMail3',
                ],
            ],
        ],
        [
            'exchangeName' => 'direct.sendDing',// 发送钉钉消息
            'exchangeFlag' => AMQP_DURABLE,
            'queue' => [
                [
                    'queueName' => 'direct.sendDing.mass',// 群发消息
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => 'key.sendDing.mass',
                ],
                [
                    'queueName' => 'direct.sendDing.single',// 单独发消息
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => 'key.sendDing.single',
                ],
            ],
        ],
    ],
    AMQP_EX_TYPE_FANOUT => [
        [
            'exchangeName' => 'fanout.sendQQ',
            'exchangeFlag' => AMQP_DURABLE,
            'queue' => [
                [
                    'queueName' => 'fanout.user1',
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => '',
                ],
                [
                    'queueName' => 'fanout.user2',
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => '',
                ],
                [
                    'queueName' => 'fanout.user3',
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => '',
                ],
            ],
        ],
    ]
];




echo '删除交换机、队列进行中...' .PHP_EOL;
try {
    // 连接
    $mqConn = new \PhpAmqpLib\Connection\AMQPStreamConnection($connConfig['host'], $connConfig['port'], $connConfig['login'], $connConfig['password'], $connConfig['vhost']);

    // 通道
    $mqChannel = $mqConn->channel();

    foreach ($config as $exchangeType => $item) {
        foreach ($item as $exchange) {
            foreach ($exchange['queue'] as $queue) {

                // 删除队列
                $mqChannel->queue_delete($queue['queueName']);
                echo '删除队列：' . $queue['queueName'] . ' 完成✅' . PHP_EOL;
            }

            // 删除交换机
            $mqChannel->exchange_delete($exchange['exchangeName']);
            echo '删除交换机：' . $exchange['exchangeName'] . ' 完成✅' . PHP_EOL;
        }
    }

    echo '交换机、队列、已清空✅';
    $mqConn->close();
}catch (Exception $e){
    exit($e->getMessage());
}

