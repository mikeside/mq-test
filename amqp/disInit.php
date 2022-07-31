<?php

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

                // 延迟队列
                [
                    'queueName' => 'direct.delay.sendMail',
                    'queueFlag' => AMQP_DURABLE,
                    'routingKey' => 'key.delay.sendMail',
                    'arguments' => [
                        'x-message-ttl' => 10000, //消息TTL 10秒后过期
                        'x-dead-letter-exchange' => 'direct.sendMail', //死信发送的交换机
                        'x-dead-letter-routing-key' => 'key.sendMail', //死信routeKey
                    ]
                ]
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
    $mqConn = new AMQPConnection($connConfig);
    $mqConn->connect();

    // 通道
    $mqChannel = new AMQPChannel($mqConn);

    foreach ($config as $exchangeType => $item) {
        foreach ($item as $exchange) {
            foreach ($exchange['queue'] as $queue) {

                // 删除队列
                $mqQueue = new AMQPQueue($mqChannel);
                $mqQueue->setName($queue['queueName']);
                $mqQueue->delete();
                echo '删除队列：' . $queue['queueName'] . ' 完成✅' . PHP_EOL;
            }

            // 删除交换机
            $mqExchange = new AMQPExchange($mqChannel);
            $mqExchange->setName($exchange['exchangeName']);
            $mqExchange->delete();
            echo '删除交换机：' . $exchange['exchangeName'] . ' 完成✅' . PHP_EOL;
        }
    }

    echo '交换机、队列、已清空✅';
    $mqConn->disconnect();
}catch (Exception $e){
    exit($e->getMessage());
}

